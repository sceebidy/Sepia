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
                'model_versi'      => 'SEPIA v1.0 (' . env('OPENAI_MODEL', 'gpt-4o') . ')',
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
    //  POST callback
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
Seluruh analisis, aktor, konteks, dan rekomendasi HARUS berfokus pada lingkup lokal Kota Medan dan sekitarnya.
Rekomendasi WAJIB selalu ditujukan kepada: Walikota Medan, Kapolrestabes Medan, Dandim 0201/BB Medan, dan Kepala Kejaksaan Negeri Medan.
Aktor yang teridentifikasi MINIMAL 3 orang atau kelompok dengan peran spesifik.
Seluruh analisis harus faktual, netral, legal, proporsional, dan berbasis bukti dari data sumber yang diberikan.
Jangan membuat fakta, nama, tanggal, atau kesimpulan yang tidak didukung data sumber.
Analisis intelijen WAJIB menggunakan framework PESTLE+C (Politik, Ekonomi, Sosial, Teknologi, Hukum/Legal, Lingkungan, Budaya/Culture).
Setiap dimensi WAJIB ditulis sebagai paragraf naratif terpisah minimal 4 kalimat — bukan bullet point.
Setiap paragraf harus mencerminkan dampak atau relevansi dimensi tersebut secara spesifik terhadap situasi di Kota Medan.
Gunakan gaya penulisan laporan intelijen formal: faktual, analitis, proporsional.Kembalikan HANYA JSON valid tanpa markdown, tanpa teks tambahan apapun.
PROMPT;

        $userPrompt = $this->buatPrompt($folder->nama, $tanggal, $konten);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model'       => env('OPENAI_MODEL', 'gpt-4o'),
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
Analisis data berikut dan buat laporan intelijen situasional yang komprehensif, faktual, netral, legal, proporsional, dan berbasis bukti.

WILAYAH/KASUS: {$namaFolder}
TANGGAL ANALISIS: {$tanggal}
WILAYAH FOKUS UTAMA: Kota Medan, Provinsi Sumatera Utara

DATA SUMBER:
{$konten}

TUJUAN LAPORAN:
Laporan ini bertujuan membantu pimpinan wilayah Kota Medan memahami situasi, mengantisipasi efek domino, serta menyiapkan kebijakan, tindakan, koordinasi, atau kegiatan yang diperlukan sesuai tugas pokok dan fungsi masing-masing jabatan.

PRINSIP UTAMA:
1. Jangan membuat fakta, nama, tanggal, lokasi, aktor, angka, atau kesimpulan yang tidak didukung data sumber.
2. Pisahkan antara fakta terverifikasi, klaim sumber, dan informasi belum terkonfirmasi.
3. Jangan menyatakan Kota Medan pasti terdampak jika tidak didukung data. Gunakan "berpotensi terdampak".
4. Jangan membuat rekomendasi yang melampaui kewenangan pejabat.
5. Jika data tidak cukup, nyatakan keterbatasan secara eksplisit.
6. Kembalikan hanya JSON valid tanpa markdown, tanpa komentar, tanpa teks tambahan.

ATURAN ID RELASIONAL:
- Setiap sumber: source_id S1, S2, S3 dst
- Setiap fakta: fakta_id F1, F2, F3 dst (minimal 3 fakta)
- Setiap aktor: actor_id A1, A2, A3 dst (minimal 3 aktor)
- Setiap timeline: event_id E1, E2, E3 dst (minimal 3 kejadian)
- Setiap risiko: risk_id R1, R2, R3 dst
- Setiap rekomendasi: recommendation_id REC1, REC2 dst (minimal 3 per jabatan)

ATURAN VALIDASI RISIKO:
- raw_score = probabilitas.skor x dampak.skor
- nilai = round((raw_score / 25) x 100)
- tingkat_risiko utama = ceil(nilai_risiko_tertinggi / 10) — skala 1-10
- Warna: 0-24="#16a34a", 25-49="#ca8a04", 50-74="#f97316", 75-100="#dc2626"

REKOMENDASI WAJIB untuk 4 jabatan (urutan tetap, minimal 3 poin per jabatan):
j1 = Walikota Medan
j2 = Kapolrestabes Medan
j3 = Dandim 0201/BB Medan (JANGAN rekomendasikan penegakan hukum)
j4 = Kepala Kejaksaan Negeri Medan (JANGAN rekomendasikan pengamanan lapangan)

Kembalikan JSON dengan struktur PERSIS seperti berikut:

{
  "tingkat_risiko": 1,
  "prediksi_situasi": "Proyeksi situasi ke depan berdasarkan data sumber.",
  "klasifikasi_dokumen": "TERBATAS",
  "ringkasan_eksekutif": "Ringkasan 2-3 kalimat faktual.",
  "kategori": "ideologi/politik/ekonomi/sosbud/hankam",
  "sub_kategori": "radikalisme/separatisme/ekstremisme/elektoral/intervensi_asing/oposisi/korupsi/investasi_asing/pencucian_uang/hoaks_sara/komunal/budaya/siber/terorisme/perbatasan",
  "metadata_wilayah": {
    "wilayah_kasus": "{$namaFolder}",
    "wilayah_fokus_utama": "Kota Medan, Provinsi Sumatera Utara",
    "tanggal_analisis": "{$tanggal}",
    "daerah_asal_kejadian": [],
    "daerah_terdampak_langsung": [],
    "daerah_berpotensi_terdampak": []
  },
  "source_register": [
    {
      "source_id": "S1",
      "judul": "Judul sumber",
      "url": "URL sumber jika tersedia",
      "sumber_media": "Nama media",
      "tanggal_publikasi": null,
      "jenis_sumber": "berita/opini/rilis_resmi/tidak_jelas",
      "reliabilitas_sumber": "tinggi/sedang/rendah",
      "catatan_reliabilitas": "Alasan penilaian."
    }
  ],
  "validasi_sumber": {
    "jumlah_sumber": 0,
    "konsistensi_sumber": "tinggi/sedang/rendah",
    "catatan_validasi": "Penjelasan kualitas sumber."
  },
  "fakta_fakta": [
    {
      "fakta_id": "F1",
      "huruf": "A",
      "judul": "Judul topik fakta",
      "isi": "Detail fakta berdasarkan data sumber — minimal 3 kalimat.",
      "kategori_fakta": "fakta_terverifikasi/klaim_sumber/informasi_belum_terkonfirmasi",
      "tanggal_kejadian": null,
      "lokasi": null,
      "actor_ids": [],
      "source_ids": [],
      "status_verifikasi": "terverifikasi_multi_sumber/hanya_satu_sumber/tidak_terverifikasi",
      "tingkat_keyakinan": "tinggi/sedang/rendah"
    }
  ],
  "aktor": [
    {
      "actor_id": "A1",
      "nama": "Nama atau label aktor",
      "inisial": "NL",
      "jenis_aktor": "individu/kelompok_masyarakat/organisasi/instansi_pemerintah/aparat_keamanan/perusahaan/media/tidak_diketahui",
      "peran_dalam_kejadian": "pelaku/korban/saksi/pejabat/penegak_hukum/narasumber/terdampak/lainnya",
      "status_hukum_atau_status_peristiwa": "tersangka/saksi/dpo/korban/pejabat/tidak_diketahui",
      "tindakan_teridentifikasi": [],
      "pengaruh_terhadap_situasi": "rendah/sedang/tinggi/tidak_dapat_ditentukan",
      "source_ids": [],
      "fakta_ids": [],
      "tingkat_keyakinan": "tinggi/sedang/rendah",
      "warna_avatar": "#be123c"
    }
  ],
  "timeline": [
    {
      "event_id": "E1",
      "tanggal": null,
      "basis_tanggal": "tanggal_kejadian/tanggal_publikasi",
      "date_precision": "hari/bulan/tahun/tidak_diketahui",
      "lokasi": null,
      "event_type": "kejadian/pernyataan/aksi_massa/kebijakan/proses_hukum/lainnya",
      "keterangan": "Deskripsi kejadian berdasarkan data sumber.",
      "actor_ids": [],
      "source_ids": [],
      "fakta_ids": [],
      "status_verifikasi": "terverifikasi_multi_sumber/hanya_satu_sumber/tidak_terverifikasi",
      "tingkat_keyakinan": "tinggi/sedang/rendah",
      "warna_dot": "#16a34a"
    }
  ],
 // BARU
"analisis_intelijen": {
  "politik": {
    "judul": "I. Dimensi Politik",
    "isi": "Paragraf naratif analisis politik minimal 4 kalimat — relevansi kebijakan, kekuasaan, stabilitas pemerintahan terhadap kasus ini di Kota Medan."
  },
  "ekonomi": {
    "judul": "II. Dimensi Ekonomi",
    "isi": "Paragraf naratif analisis ekonomi minimal 4 kalimat — dampak finansial, investasi, perdagangan, ketenagakerjaan di Kota Medan."
  },
  "sosial": {
    "judul": "III. Dimensi Sosial",
    "isi": "Paragraf naratif analisis sosial minimal 4 kalimat — dampak terhadap masyarakat, kelompok rentan, kohesi sosial di Kota Medan."
  },
  "teknologi": {
    "judul": "IV. Dimensi Teknologi",
    "isi": "Paragraf naratif analisis teknologi minimal 4 kalimat — peran digital, infrastruktur, media siber dalam kasus ini di Kota Medan."
  },
  "hukum": {
    "judul": "V. Dimensi Hukum",
    "isi": "Paragraf naratif analisis hukum minimal 4 kalimat — potensi pelanggaran, regulasi terkait, risiko hukum di Kota Medan."
  },
  "lingkungan": {
    "judul": "VI. Dimensi Lingkungan",
    "isi": "Paragraf naratif analisis lingkungan minimal 4 kalimat — dampak terhadap lingkungan hidup, SDA, tata ruang di Kota Medan."
  },
  "budaya": {
    "judul": "VII. Dimensi Budaya",
    "isi": "Paragraf naratif analisis budaya minimal 4 kalimat — dampak terhadap nilai, adat, identitas budaya lokal Medan."
  }
}
  "analisis_dampak_lintas_daerah": {
    "status_indikasi": "ada_indikasi/belum_terdapat_indikasi/tidak_dapat_ditentukan",
    "ringkasan": "Ringkasan dampak lintas daerah terhadap Kota Medan.",
    "daerah_fokus_utama": "Kota Medan",
    "kemungkinan_dampak": "rendah/sedang/tinggi",
    "tingkat_dampak": "rendah/sedang/tinggi/kritis",
    "dasar_fakta": [],
    "indikator_peringatan_dini_lintas_daerah": []
  },
  "risk": [
    {
      "risk_id": "R1",
      "label": "Nama risiko spesifik",
      "deskripsi_risiko": "Penjelasan berdasarkan data sumber.",
      "source_ids": [],
      "linked_fakta_ids": [],
      "linked_event_ids": [],
      "indikator_risiko": [],
      "probabilitas": {
        "skor": 3,
        "label": "mungkin",
        "alasan": "Alasan skor probabilitas."
      },
      "dampak": {
        "skor": 3,
        "label": "sedang",
        "alasan": "Alasan skor dampak."
      },
      "raw_score": 9,
      "nilai": 36,
      "tingkat_risiko": "sedang",
      "warna": "#ca8a04",
      "horizon_waktu": "7 hari",
      "tingkat_keyakinan": "tinggi/sedang/rendah"
    }
  ],
  "early_warning": [
    {
      "indikator": "Indikator peringatan dini spesifik dan terukur.",
      "kategori": "sosial/keamanan/hukum/ekonomi/politik/digital_informasi/lainnya",
      "wilayah_monitoring": ["Kota Medan"],
      "source_ids": [],
      "risk_ids": []
    }
  ],
  "swot": {
    "S": [
      {"poin": "Kekuatan spesifik 1.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Kekuatan spesifik 2.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Kekuatan spesifik 3.", "dasar_fakta": [], "source_ids": []}
    ],
    "W": [
      {"poin": "Kelemahan spesifik 1.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Kelemahan spesifik 2.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Kelemahan spesifik 3.", "dasar_fakta": [], "source_ids": []}
    ],
    "O": [
      {"poin": "Peluang spesifik 1.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Peluang spesifik 2.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Peluang spesifik 3.", "dasar_fakta": [], "source_ids": []}
    ],
    "T": [
      {"poin": "Ancaman spesifik 1.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Ancaman spesifik 2.", "dasar_fakta": [], "source_ids": []},
      {"poin": "Ancaman spesifik 3.", "dasar_fakta": [], "source_ids": []}
    ]
  },
  "jabatan_rekomendasi": {
    "j1": {
      "nama_jabatan": "Walikota Medan",
      "poin": [
        {
          "recommendation_id": "REC1",
          "rekomendasi": "Rekomendasi spesifik dan actionable untuk Walikota.",
          "dasar_fakta": [],
          "linked_risk_ids": [],
          "tindakan_yang_disarankan": "Tindakan sesuai kewenangan Walikota.",
          "prioritas": "tinggi/sedang/rendah",
          "batas_waktu": "segera/1x24 jam/3 hari/7 hari/30 hari",
          "risiko_jika_tidak_dilakukan": "Risiko jika tidak dijalankan.",
          "tingkat_keyakinan": "tinggi/sedang/rendah"
        }
      ]
    },
    "j2": {
      "nama_jabatan": "Kapolrestabes Medan",
      "poin": [
        {
          "recommendation_id": "REC2",
          "rekomendasi": "Rekomendasi spesifik dan actionable untuk Kapolrestabes.",
          "dasar_fakta": [],
          "linked_risk_ids": [],
          "tindakan_yang_disarankan": "Tindakan sesuai kewenangan Kapolrestabes.",
          "prioritas": "tinggi/sedang/rendah",
          "batas_waktu": "segera/1x24 jam/3 hari/7 hari/30 hari",
          "risiko_jika_tidak_dilakukan": "Risiko jika tidak dijalankan.",
          "tingkat_keyakinan": "tinggi/sedang/rendah"
        }
      ]
    },
    "j3": {
      "nama_jabatan": "Dandim 0201/BB Medan",
      "poin": [
        {
          "recommendation_id": "REC3",
          "rekomendasi": "Rekomendasi sesuai tupoksi binwil — deteksi dini, komunikasi sosial, pembinaan teritorial.",
          "dasar_fakta": [],
          "linked_risk_ids": [],
          "tindakan_yang_disarankan": "Tindakan sesuai kewenangan Dandim.",
          "prioritas": "tinggi/sedang/rendah",
          "batas_waktu": "segera/1x24 jam/3 hari/7 hari/30 hari",
          "risiko_jika_tidak_dilakukan": "Risiko jika tidak dijalankan.",
          "tingkat_keyakinan": "tinggi/sedang/rendah"
        }
      ]
    },
    "j4": {
      "nama_jabatan": "Kepala Kejaksaan Negeri Medan",
      "poin": [
        {
          "recommendation_id": "REC4",
          "rekomendasi": "Rekomendasi sesuai tupoksi hukum — pertimbangan hukum, mitigasi risiko hukum kebijakan.",
          "dasar_fakta": [],
          "linked_risk_ids": [],
          "tindakan_yang_disarankan": "Tindakan sesuai kewenangan Kajari.",
          "prioritas": "tinggi/sedang/rendah",
          "batas_waktu": "segera/1x24 jam/3 hari/7 hari/30 hari",
          "risiko_jika_tidak_dilakukan": "Risiko jika tidak dijalankan.",
          "tingkat_keyakinan": "tinggi/sedang/rendah"
        }
      ]
    }
  },
  "confidence": {
    "kelengkapan_data": 75,
    "konsistensi_sumber": 80,
    "kualitas_dokumen": 70,
    "kedalaman_analisis": 85,
    "catatan_confidence": "Penjelasan skor confidence."
  },
  "catatan_analis": "Catatan kritis berbasis data dari analis senior.",
  "catatan_akhir": {
    "kesimpulan_umum": "Kesimpulan umum berdasarkan fakta dan risiko.",
    "prioritas_monitoring": [],
    "peringatan_keterbatasan": "Keterbatasan utama data dan analisis."
  }
}
PROMPT;
    }

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: Simpan hasil ke database
    // ═══════════════════════════════════════════════════════════
    private function simpanHasil(AnalisisKasus $analisis, array $data): void
    {
        // ── Hitung tingkat risiko dari nilai tertinggi ──
        $tingkatRisiko = $data['tingkat_risiko'] ?? 5;
        if (!empty($data['risk']) && is_array($data['risk'])) {
            $nilaiTertinggi = max(array_column($data['risk'], 'nilai') ?: [0]);
            if ($nilaiTertinggi > 0) {
                $tingkatRisiko = ceil($nilaiTertinggi / 10);
            }
        }

        // ── Update analisis utama ──
        $analisis->update([
            'tingkat_risiko'      => $tingkatRisiko,
            'prediksi_vonis'      => $data['prediksi_situasi']    ?? $data['prediksi_vonis'] ?? null,
            'ringkasan_eksekutif' => $data['ringkasan_eksekutif'] ?? null,
            'analisis_intelijen'  => is_array($data['analisis_intelijen'] ?? null)
                                        ? json_encode($data['analisis_intelijen'])
                                        : ($data['analisis_intelijen'] ?? null),
            'catatan_analis'      => $data['catatan_analis']      ?? null,
            'klasifikasi_dokumen' => $data['klasifikasi_dokumen'] ?? 'TERBATAS',
            'fakta_fakta'         => json_encode($data['fakta_fakta']         ?? []),
            'jabatan_rekomendasi' => json_encode($data['jabatan_rekomendasi'] ?? []),
            'early_warning'       => json_encode(
                array_map(fn($ew) => is_array($ew) ? ($ew['indikator'] ?? '') : $ew,
                    $data['early_warning'] ?? [])
            ),
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
        $risikoLabel = $tingkatRisiko >= 7 ? 'tinggi' : ($tingkatRisiko >= 4 ? 'sedang' : 'rendah');

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
            foreach ($data['swot'][$tipe] ?? [] as $i => $item) {
                $isi = is_array($item) ? ($item['poin'] ?? '') : $item;
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
            $nama = $aktor['nama'] ?? '';
            if (!empty($nama)) {
                $status = $aktor['status_hukum_atau_status_peristiwa'] ?? $aktor['status'] ?? 'saksi';
                if (!in_array($status, ['tersangka', 'saksi', 'dpo'])) {
                    $status = 'saksi';
                }
                AktorKasus::create([
                    'analisis_id'  => $analisis->id,
                    'nama'         => $nama,
                    'inisial'      => substr($aktor['inisial'] ?? strtoupper(substr($nama, 0, 2)), 0, 10),
                    'peran'        => $aktor['peran_dalam_kejadian'] ?? $aktor['peran'] ?? '-',
                    'status'       => $status,
                    'warna_avatar' => $aktor['warna_avatar'] ?? '#1a5c2e',
                ]);
            }
        }

        // ── Timeline ──
        foreach ($data['timeline'] ?? [] as $i => $tl) {
            TimelineKasus::create([
                'analisis_id' => $analisis->id,
                'tanggal'     => $tl['tanggal'] ?? now()->format('Y-m-d'),
                'keterangan'  => $tl['keterangan'] ?? '-',
                'warna_dot'   => $tl['warna_dot']  ?? '#16a34a',
                'urutan'      => $i,
            ]);
        }

        // ── Rekomendasi ──
        $urutan = 0;
        foreach ($data['jabatan_rekomendasi'] ?? [] as $jabatan) {
            $namaJabatan = $jabatan['nama_jabatan'] ?? '-';
            foreach ($jabatan['poin'] ?? [] as $poin) {
                $teks = is_array($poin) ? ($poin['rekomendasi'] ?? '') : $poin;
                if (!empty($teks)) {
                    RekomendasiKasus::create([
                        'analisis_id' => $analisis->id,
                        'judul'       => $namaJabatan,
                        'deskripsi'   => $teks,
                        'prioritas'   => is_array($poin) ? ($poin['prioritas'] ?? 'tinggi') : 'tinggi',
                        'urutan'      => $urutan++,
                    ]);
                }
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
                    'keterangan'  => $risk['deskripsi_risiko'] ?? $risk['keterangan'] ?? null,
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