<?php

namespace App\Http\Controllers;

use App\Models\AnalisisKasus;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PresentasiInteligenController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  GET /presentasi-intelijen
    // ═══════════════════════════════════════════════════════════
    public function index()
    {
        // Analisis untuk dipilih sebagai bahan presentasi
        $analisisList = AnalisisKasus::with(['riskItems', 'folder'])
            ->whereNotNull('ringkasan_eksekutif')
            ->orderByDesc('tanggal_analisis')
            ->get();

        // Issue aktif untuk slide situasi terkini
        $issueAktif = Issue::where('status', 'aktif')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Statistik ringkas untuk slide pembuka
        $stats = [
            'total_kasus'    => $analisisList->count(),
            'risiko_tinggi'  => $analisisList->where('tingkat_risiko', '>=', 7)->count(),
            'total_issue'    => Issue::where('status', 'aktif')->count(),
            'wilayah'        => 'Kota Medan, Sumatera Utara',
            'tanggal'        => now()->translatedFormat('d F Y'),
        ];

        return view('presentasi-intelijen', compact('analisisList', 'issueAktif', 'stats'));
    }

    // ═══════════════════════════════════════════════════════════
    //  GET /presentasi-intelijen/{analisis}
    //  Mode presentasi fullscreen untuk satu analisis
    // ═══════════════════════════════════════════════════════════
    public function show(AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'confidence', 'riskItems', 'folder']);

        // Decode semua field JSON
        $fakta        = $this->decodeJson($analisis->fakta_fakta);
        $earlyWarning = $this->decodeJson($analisis->early_warning);
        $jabatanRek   = $this->decodeJson($analisis->jabatan_rekomendasi);

        // Decode PESTLE
        $pestle  = [];
        $decoded = $this->decodeJson($analisis->analisis_intelijen);
        if (isset($decoded['politik'])) {
            $pestle = array_map(
                fn($v) => is_array($v) ? ($v['isi'] ?? $v['narasi'] ?? '') : $v,
                $decoded
            );
        }

        // Susun slide deck
        $slides = $this->buildSlides($analisis, $fakta, $pestle);

        return view('presentasi-intelijen-show', compact(
            'analisis', 'fakta', 'earlyWarning', 'jabatanRek', 'pestle', 'slides'
        ));
    }

    // ═══════════════════════════════════════════════════════════
    //  GET /presentasi-intelijen/{analisis}/slideshow
    //  JSON endpoint untuk slide runner JS
    // ═══════════════════════════════════════════════════════════
    public function slideshow(AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'riskItems']);

        $fakta   = $this->decodeJson($analisis->fakta_fakta);
        $decoded = $this->decodeJson($analisis->analisis_intelijen);
        $pestle  = isset($decoded['politik'])
            ? array_map(fn($v) => is_array($v) ? ($v['isi'] ?? '') : $v, $decoded)
            : [];

        $slides = $this->buildSlides($analisis, $fakta, $pestle);

        return response()->json(['slides' => $slides]);
    }

    // ─── Private: Build slide structure ───────────────────────
    private function buildSlides(AnalisisKasus $analisis, array $fakta, array $pestle): array
    {
        $slides = [];

        // Slide 1 — Cover
        $slides[] = [
            'tipe'    => 'cover',
            'judul'   => $analisis->judul,
            'perihal' => $analisis->perihal,
            'periode' => $analisis->periode,
            'wilayah' => $analisis->wilayah,
            'klasifikasi' => $analisis->klasifikasi_dokumen,
        ];

        // Slide 2 — Ringkasan Eksekutif
        $slides[] = [
            'tipe'    => 'ringkasan',
            'judul'   => 'Ringkasan Eksekutif',
            'konten'  => $analisis->ringkasan_eksekutif,
            'risiko'  => $analisis->tingkat_risiko,
        ];

        // Slide 3 — Fakta Utama
        if (!empty($fakta)) {
            $slides[] = [
                'tipe'   => 'fakta',
                'judul'  => 'Fakta-Fakta Utama',
                'items'  => array_slice($fakta, 0, 5),
            ];
        }

        // Slide 4-10 — PESTLE per dimensi
        $pestleLabel = [
            'politik'    => 'I. Dimensi Politik',
            'ekonomi'    => 'II. Dimensi Ekonomi',
            'sosial'     => 'III. Dimensi Sosial',
            'teknologi'  => 'IV. Dimensi Teknologi',
            'hukum'      => 'V. Dimensi Hukum',
            'lingkungan' => 'VI. Dimensi Lingkungan',
            'budaya'     => 'VII. Dimensi Budaya',
        ];
        foreach ($pestleLabel as $key => $label) {
            if (!empty($pestle[$key])) {
                $slides[] = [
                    'tipe'   => 'pestle',
                    'judul'  => $label,
                    'konten' => $pestle[$key],
                    'key'    => $key,
                ];
            }
        }

        // Slide — SWOT
        $swot = $analisis->swotItems->groupBy('tipe');
        if ($swot->isNotEmpty()) {
            $slides[] = [
                'tipe'  => 'swot',
                'judul' => 'Analisis SWOT',
                'S'     => $swot->get('S', collect())->pluck('isi')->toArray(),
                'W'     => $swot->get('W', collect())->pluck('isi')->toArray(),
                'O'     => $swot->get('O', collect())->pluck('isi')->toArray(),
                'T'     => $swot->get('T', collect())->pluck('isi')->toArray(),
            ];
        }

        // Slide — Risk Assessment
        if ($analisis->riskItems->isNotEmpty()) {
            $slides[] = [
                'tipe'  => 'risiko',
                'judul' => 'Penilaian Risiko',
                'items' => $analisis->riskItems->map(fn($r) => [
                    'label' => $r->label,
                    'nilai' => $r->nilai,
                    'warna' => $r->warna,
                ])->toArray(),
            ];
        }

        // Slide — Aktor
        if ($analisis->aktor->isNotEmpty()) {
            $slides[] = [
                'tipe'  => 'aktor',
                'judul' => 'Peta Aktor',
                'items' => $analisis->aktor->map(fn($a) => [
                    'nama'         => $a->nama,
                    'inisial'      => $a->inisial,
                    'peran'        => $a->peran,
                    'status'       => $a->status,
                    'warna_avatar' => $a->warna_avatar,
                ])->toArray(),
            ];
        }

        // Slide — Rekomendasi per jabatan
        $rekomendasi = $analisis->rekomendasi->groupBy('judul');
        if ($rekomendasi->isNotEmpty()) {
            foreach ($rekomendasi as $jabatan => $poin) {
                $slides[] = [
                    'tipe'    => 'rekomendasi',
                    'judul'   => 'Rekomendasi: ' . $jabatan,
                    'jabatan' => $jabatan,
                    'items'   => $poin->pluck('deskripsi')->toArray(),
                ];
            }
        }

        // Slide — Penutup
        $slides[] = [
            'tipe'    => 'penutup',
            'judul'   => 'Catatan Analis',
            'konten'  => $analisis->catatan_analis,
            'periode' => $analisis->periode,
        ];

        return $slides;
    }

    // ─── Helper ───────────────────────────────────────────────
    private function decodeJson($value): array
    {
        if (!$value) return [];
        return is_array($value) ? $value : (json_decode($value, true) ?? []);
    }
}