<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\AnalisisKasus;
use App\Models\SwotItem;
use App\Models\AktorKasus;
use App\Models\TimelineKasus;
use App\Models\RekomendasiKasus;
use App\Models\ConfidenceKasus;
use App\Models\RiskAssessment;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalisisController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  POST /datapool/{folder}/analisis
    // ═══════════════════════════════════════════════════════════
    public function store(Request $request, Folder $folder)
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $items = $folder->items()->get();

        if ($items->isEmpty()) {
            return response()->json(['error' => 'Folder tidak ada sumber data'], 400);
        }

        try {
            $existing = AnalisisKasus::where('folder_id', $folder->id)->first();
            if ($existing) {
                SwotItem::where('analisis_id', $existing->id)->delete();
                AktorKasus::where('analisis_id', $existing->id)->delete();
                TimelineKasus::where('analisis_id', $existing->id)->delete();
                RekomendasiKasus::where('analisis_id', $existing->id)->delete();
                RiskAssessment::where('analisis_id', $existing->id)->delete();
                ConfidenceKasus::where('analisis_id', $existing->id)->delete();
                Issue::where('sumber', 'SEPIA RPI')->where('judul', $existing->judul)->delete();
                $existing->delete();
            }

            $analisis = AnalisisKasus::create([
                'folder_id'        => $folder->id,
                'judul'            => $folder->nama,
                'perihal'          => 'Perkembangan Situasi ' . $folder->nama,
                'periode'          => now()->format('d M Y'),
                'wilayah'          => 'Kota Medan, Sumatera Utara',
                'tanggal_analisis' => now(),
                'tingkat_risiko'   => 0,
                'jumlah_sumber'    => $items->count(),
                'model_versi'      => 'SEPIA v1.0 (' . env('OPENAI_MODEL', 'gpt-4o-mini') . ')',
            ]);

            $this->prosesDenganAI($folder, $items, $analisis);

            return response()->json([
                'success'     => true,
                'analisis_id' => $analisis->id,
                'folder_id'   => $folder->id,
            ]);

        } catch (\Exception $e) {
            Log::error('[SEPIA] Analisis error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════
    //  PATCH update-info
    // ═══════════════════════════════════════════════════════════
    public function updateInfo(Request $request, Folder $folder, AnalisisKasus $analisis)
    {
        $analisis->update([
            'perihal' => $request->perihal,
            'periode' => $request->periode,
            'wilayah' => $request->wilayah,
        ]);
        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════
    //  GET show
    // ═══════════════════════════════════════════════════════════
    public function show(Folder $folder, AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'confidence', 'riskItems']);

        if (request()->wantsJson()) {
            return response()->json(['analisis' => $analisis]);
        }

        return view('analisis-hasil', compact('folder', 'analisis'));
    }

    // ═══════════════════════════════════════════════════════════
    //  POST callback (n8n backward compat)
    // ═══════════════════════════════════════════════════════════
    public function callback(Request $request)
    {
        try {
            $analisis = AnalisisKasus::findOrFail($request->analisis_id);
            $this->simpanHasil($analisis, $request->all());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('[SEPIA] Callback error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: Proses dengan OpenAI
    // ═══════════════════════════════════════════════════════════
    private function prosesDenganAI($folder, $items, $analisis)
    {
        $konten  = $this->siapkanKonten($items);
        $tanggal = now()->translatedFormat('d F Y');

        $systemPrompt = <<<PROMPT
Kamu adalah Perwira Analis Intelijen Senior yang bertugas di Kota Medan, Sumatera Utara, Indonesia.
Seluruh analisis, aktor, konteks, dan rekomendasi HARUS berfokus pada lingkup lokal Kota Medan dan sekitarnya — bukan nasional.
Rekomendasi WAJIB selalu ditujukan kepada empat jabatan berikut secara berurutan:
1. Dandim 0201/BB Medan
2. Kepala Kejaksaan Negeri Medan
3. Walikota Medan
4. Kapolrestabes Medan
Sesuaikan isi rekomendasi dengan tugas pokok dan fungsi masing-masing jabatan tersebut.
Aktor yang teridentifikasi MINIMAL 3 orang dengan peran spesifik di lingkungan Kota Medan.
Balas HANYA dengan JSON valid tanpa markdown, tanpa teks tambahan apapun.
PROMPT;

        $userPrompt = $this->buatPrompt($folder->nama, $tanggal, $konten);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model'       => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'temperature' => 0.3,
            'max_tokens'  => 8000,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userPrompt],
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI error: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content', '');
        $content = trim(preg_replace('/```json|```/', '', $content));

        preg_match('/\{.*\}/s', $content, $matches);
        $parsed = json_decode($matches[0] ?? $content, true);

        if (!$parsed) {
            throw new \Exception('Gagal parse JSON dari OpenAI: ' . substr($content, 0, 300));
        }

        $this->simpanHasil($analisis, $parsed);
    }

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: Siapkan konten
    // ═══════════════════════════════════════════════════════════
    private function siapkanKonten($items): string
    {
        $kontenList = [];

        foreach ($items as $item) {
            if ($item->tipe === 'link') {
                $kontenList[] = "[LINK] {$item->judul}: {$item->konten}";
                try {
                    $html = Http::timeout(8)->get($item->konten)->body();
                    $teks = strip_tags($html);
                    $teks = trim(substr(preg_replace('/\s+/', ' ', $teks), 0, 3000));
                    if (strlen($teks) > 100) {
                        $kontenList[] = "[ISI ARTIKEL] {$teks}";
                    }
                } catch (\Exception $e) {}

            } elseif ($item->tipe === 'catatan') {
                $kontenList[] = "[CATATAN] {$item->judul}: {$item->konten}";

            } elseif ($item->tipe === 'file') {
                $kontenList[] = "[FILE] {$item->judul}: {$item->file_nama}";
                if ($item->file_path) {
                    try {
                        $extractor = new \App\Services\FileExtractorService();
                        $isiFile   = $extractor->extract($item->file_path, $item->file_nama ?? '');
                        if ($isiFile) {
                            $kontenList[] = "[ISI DOKUMEN — {$item->judul}]\n{$isiFile}";
                        } elseif ($item->hasil_rangkuman) {
                            $kontenList[] = "[RANGKUMAN] {$item->hasil_rangkuman}";
                        }
                    } catch (\Exception $e) {
                        if ($item->hasil_rangkuman) {
                            $kontenList[] = "[RANGKUMAN] {$item->hasil_rangkuman}";
                        }
                    }
                }
            }
        }

        return implode("\n\n", $kontenList);
    }

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: Buat prompt
    // ═══════════════════════════════════════════════════════════
    private function buatPrompt(string $namaFolder, string $tanggal, string $konten): string
    {
        return <<<PROMPT
Analisis data berikut dan buat laporan intelijen situasional yang komprehensif dengan konteks lokal Kota Medan:

KASUS/ISU: {$namaFolder}
LOKASI: Kota Medan, Sumatera Utara
TANGGAL: {$tanggal}

DATA SUMBER:
{$konten}

Kembalikan JSON dengan struktur berikut (semua field wajib diisi dengan konten substantif dan relevan dengan Kota Medan):
{
  "tingkat_risiko": (angka 1-10 berdasarkan analisis situasi di Medan),
  "prediksi_vonis": "proyeksi situasi ke depan secara spesifik di Kota Medan",
  "klasifikasi_dokumen": "RAHASIA/TERBATAS/BIASA",
  "ringkasan_eksekutif": "Ringkasan 2-3 kalimat yang tajam tentang situasi di Kota Medan",
  "kategori": "pilih salah satu: ideologi/politik/ekonomi/sosbud/hankam",
  "sub_kategori": "pilih salah satu: radikalisme/separatisme/ekstremisme/elektoral/intervensi_asing/oposisi/korupsi/investasi_asing/pencucian_uang/hoaks_sara/komunal/budaya/siber/terorisme/perbatasan",
  "fakta_fakta": [
    {
      "huruf": "A",
      "judul": "Judul Topik Fakta di Medan",
      "isi": "Detail lengkap fakta — tanggal, lokasi spesifik di Medan, pelaku, kronologi. Minimal 3-4 kalimat."
    }
  ],
  "analisis_intelijen": "Analisis mendalam 5-6 kalimat tentang situasi di Kota Medan: potensi kerawanan lokal, dinamika sosial-politik Medan, pola yang teridentifikasi, dan proyeksi ke depan.",
  "jabatan_rekomendasi": {
    "j1": {
      "nama_jabatan": "Dandim 0201/BB Medan",
      "poin": [
        "Rekomendasi spesifik sesuai tupoksi Dandim terkait situasi ini",
        "Rekomendasi kedua untuk Dandim",
        "Rekomendasi ketiga untuk Dandim"
      ]
    },
    "j2": {
      "nama_jabatan": "Kepala Kejaksaan Negeri Medan",
      "poin": [
        "Rekomendasi spesifik sesuai tupoksi Kajari terkait situasi ini",
        "Rekomendasi kedua untuk Kajari",
        "Rekomendasi ketiga untuk Kajari"
      ]
    },
    "j3": {
      "nama_jabatan": "Walikota Medan",
      "poin": [
        "Rekomendasi spesifik sesuai kewenangan Walikota terkait situasi ini",
        "Rekomendasi kedua untuk Walikota",
        "Rekomendasi ketiga untuk Walikota"
      ]
    },
    "j4": {
      "nama_jabatan": "Kapolrestabes Medan",
      "poin": [
        "Rekomendasi spesifik sesuai tupoksi Kapolrestabes terkait situasi ini",
        "Rekomendasi kedua untuk Kapolrestabes",
        "Rekomendasi ketiga untuk Kapolrestabes"
      ]
    }
  },
  "early_warning": [
    "Indikator peringatan dini 1 yang spesifik dan terukur di Medan",
    "Indikator peringatan dini 2",
    "Indikator peringatan dini 3",
    "Indikator peringatan dini 4"
  ],
  "catatan_analis": "Catatan kritis dari sudut pandang analis senior tentang situasi di Medan — 1-2 kalimat.",
  "aktor": [
    {"nama": "Nama Lengkap 1", "inisial": "NL", "peran": "Peran spesifik dalam kasus di Medan", "status": "tersangka/saksi/dpo", "warna_avatar": "#be123c"},
    {"nama": "Nama Lengkap 2", "inisial": "NL", "peran": "Peran spesifik dalam kasus di Medan", "status": "saksi", "warna_avatar": "#1e6fa3"},
    {"nama": "Nama Lengkap 3", "inisial": "NL", "peran": "Peran spesifik dalam kasus di Medan", "status": "saksi", "warna_avatar": "#065f46"}
  ],
  "timeline": [
    {"tanggal": "YYYY-MM-DD", "keterangan": "Deskripsi kejadian spesifik di Medan dengan lokasi", "warna_dot": "#16a34a"}
  ],
  "swot": {
    "S": ["Kekuatan internal yang mendukung penanganan di Medan", "Kekuatan 2", "Kekuatan 3"],
    "W": ["Kelemahan internal di Medan", "Kelemahan 2", "Kelemahan 3"],
    "O": ["Peluang eksternal yang dapat dimanfaatkan di Medan", "Peluang 2"],
    "T": ["Ancaman eksternal yang perlu diwaspadai di Medan", "Ancaman 2", "Ancaman 3"]
  },
  "risk": [
    {"label": "Nama Risiko Spesifik di Medan", "nilai": 75, "warna": "#dc2626", "keterangan": "Penjelasan detail risiko ini di konteks Medan"}
  ],
  "confidence": {
    "kelengkapan_data": 75,
    "konsistensi_sumber": 80,
    "kualitas_dokumen": 70,
    "kedalaman_analisis": 85
  }
}
PROMPT;
    }

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: Simpan hasil ke database
    // ═══════════════════════════════════════════════════════════
    private function simpanHasil(AnalisisKasus $analisis, array $data): void
    {
        // ── Update analisis utama ──
        $analisis->update([
            'tingkat_risiko'      => $data['tingkat_risiko']      ?? 5,
            'prediksi_vonis'      => $data['prediksi_vonis']      ?? null,
            'ringkasan_eksekutif' => $data['ringkasan_eksekutif'] ?? null,
            'analisis_intelijen'  => $data['analisis_intelijen']  ?? null,
            'catatan_analis'      => $data['catatan_analis']      ?? null,
            'klasifikasi_dokumen' => $data['klasifikasi_dokumen'] ?? 'TERBATAS',
            'fakta_fakta'         => json_encode($data['fakta_fakta']         ?? []),
            'jabatan_rekomendasi' => json_encode($data['jabatan_rekomendasi'] ?? []),
            'early_warning'       => json_encode($data['early_warning']       ?? []),
        ]);

        // ── Simpan ke tabel issues untuk dashboard ──
        $kategoriValid    = ['ideologi', 'politik', 'ekonomi', 'sosbud', 'hankam'];
        $subKategoriValid = [
            'radikalisme', 'separatisme', 'ekstremisme',
            'elektoral', 'intervensi_asing', 'oposisi',
            'korupsi', 'investasi_asing', 'pencucian_uang',
            'hoaks_sara', 'komunal', 'budaya',
            'siber', 'terorisme', 'perbatasan',
        ];

        $kategori    = strtolower(trim($data['kategori']     ?? ''));
        $subKategori = strtolower(trim($data['sub_kategori'] ?? ''));
        $risiko      = (float) ($data['tingkat_risiko'] ?? 5);
        $risikoLabel = $risiko >= 7 ? 'tinggi' : ($risiko >= 4 ? 'sedang' : 'rendah');

        if (in_array($kategori, $kategoriValid) && in_array($subKategori, $subKategoriValid)) {
            Issue::create([
                'judul'        => $analisis->judul,
                'deskripsi'    => $analisis->ringkasan_eksekutif ?? $analisis->judul,
                'kategori'     => $kategori,
                'sub_kategori' => $subKategori,
                'risiko'       => $risikoLabel,
                'status'       => 'aktif',
                'wilayah'      => 'Kota Medan, Sumatera Utara',
                'sumber'       => 'SEPIA RPI',
            ]);
        } else {
            Log::warning('[SEPIA] Kategori tidak valid: ' . $kategori . ' / ' . $subKategori);
        }

        // ── SWOT ──
        foreach (['S', 'W', 'O', 'T'] as $tipe) {
            foreach ($data['swot'][$tipe] ?? [] as $i => $isi) {
                if (trim($isi)) {
                    SwotItem::create([
                        'analisis_id' => $analisis->id,
                        'tipe'        => $tipe,
                        'isi'         => $isi,
                        'urutan'      => $i,
                    ]);
                }
            }
        }

        // ── Aktor ──
        foreach ($data['aktor'] ?? [] as $aktor) {
            if (!empty($aktor['nama'])) {
                AktorKasus::create([
                    'analisis_id'  => $analisis->id,
                    'nama'         => $aktor['nama'],
                    'inisial'      => $aktor['inisial'] ?? strtoupper(substr($aktor['nama'], 0, 2)),
                    'peran'        => $aktor['peran']   ?? '-',
                    'status'       => in_array($aktor['status'] ?? '', ['tersangka', 'saksi', 'dpo'])
                                        ? $aktor['status'] : 'saksi',
                    'warna_avatar' => $aktor['warna_avatar'] ?? '#1a5c2e',
                ]);
            }
        }

        // ── Timeline ──
        foreach ($data['timeline'] ?? [] as $i => $tl) {
            if (!empty($tl['tanggal'])) {
                TimelineKasus::create([
                    'analisis_id' => $analisis->id,
                    'tanggal'     => $tl['tanggal'],
                    'keterangan'  => $tl['keterangan'] ?? '-',
                    'warna_dot'   => $tl['warna_dot']  ?? '#16a34a',
                    'urutan'      => $i,
                ]);
            }
        }

        // ── Rekomendasi per jabatan ──
        $urutan = 0;
        foreach ($data['jabatan_rekomendasi'] ?? [] as $jabatan) {
            $namaJabatan = $jabatan['nama_jabatan'] ?? '-';
            foreach ($jabatan['poin'] ?? [] as $poin) {
                RekomendasiKasus::create([
                    'analisis_id' => $analisis->id,
                    'judul'       => $namaJabatan,
                    'deskripsi'   => $poin,
                    'prioritas'   => 'tinggi',
                    'urutan'      => $urutan++,
                ]);
            }
        }

        // ── Risk Assessment ──
        foreach ($data['risk'] ?? [] as $i => $risk) {
            if (!empty($risk['label'])) {
                RiskAssessment::create([
                    'analisis_id' => $analisis->id,
                    'label'       => $risk['label'],
                    'nilai'       => $risk['nilai']      ?? 0,
                    'warna'       => $risk['warna']      ?? '#dc2626',
                    'keterangan'  => $risk['keterangan'] ?? null,
                    'urutan'      => $i,
                ]);
            }
        }

        // ── Confidence ──
        $conf = $data['confidence'] ?? [];
        ConfidenceKasus::create([
            'analisis_id'        => $analisis->id,
            'kelengkapan_data'   => $conf['kelengkapan_data']   ?? 60,
            'konsistensi_sumber' => $conf['konsistensi_sumber'] ?? 60,
            'kualitas_dokumen'   => $conf['kualitas_dokumen']   ?? 60,
            'kedalaman_analisis' => $conf['kedalaman_analisis'] ?? 60,
        ]);
    }
}