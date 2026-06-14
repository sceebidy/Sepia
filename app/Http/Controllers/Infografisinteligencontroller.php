<?php

namespace App\Http\Controllers;

use App\Models\AnalisisKasus;
use App\Models\Issue;
use App\Models\RiskAssessment;
use App\Models\SwotItem;
use Illuminate\Http\Request;

class InfografisInteligenController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  GET /infografis-intelijen
    // ═══════════════════════════════════════════════════════════
    public function index()
    {
        // Data agregat untuk visualisasi
        $analisisList = AnalisisKasus::with(['riskItems'])
            ->whereNotNull('ringkasan_eksekutif')
            ->orderByDesc('tanggal_analisis')
            ->get();

        // Distribusi risiko untuk chart
        $distribusiRisiko = [
            'tinggi' => $analisisList->where('tingkat_risiko', '>=', 7)->count(),
            'sedang' => $analisisList->whereBetween('tingkat_risiko', [4, 6])->count(),
            'rendah' => $analisisList->where('tingkat_risiko', '<', 4)->count(),
        ];

        // Issue per kategori untuk heatmap
        $issuePerKategori = Issue::where('status', 'aktif')
            ->selectRaw('kategori, sub_kategori, COUNT(*) as jumlah, MAX(risiko) as risiko_tertinggi')
            ->groupBy('kategori', 'sub_kategori')
            ->orderByDesc('jumlah')
            ->get();

        // Risk items untuk bubble chart — top 10 nilai tertinggi
        $topRisiko = RiskAssessment::with('analisis')
            ->orderByDesc('nilai')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'label'       => $r->label,
                'nilai'       => $r->nilai,
                'warna'       => $r->warna,
                'analisis'    => $r->analisis->judul ?? '-',
                'analisis_id' => $r->analisis_id,
            ]);

        // Tren analisis per bulan (6 bulan terakhir)
        $trenBulanan = AnalisisKasus::selectRaw(
                "DATE_FORMAT(tanggal_analisis, '%Y-%m') as bulan, COUNT(*) as jumlah"
            )
            ->where('tanggal_analisis', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('jumlah', 'bulan')
            ->toArray();

        // SWOT agregat — frekuensi per tipe
        $swotCount = SwotItem::selectRaw('tipe, COUNT(*) as jumlah')
            ->groupBy('tipe')
            ->pluck('jumlah', 'tipe')
            ->toArray();

        return view('infografis-intelijen', compact(
            'analisisList',
            'distribusiRisiko',
            'issuePerKategori',
            'topRisiko',
            'trenBulanan',
            'swotCount'
        ));
    }

    // ═══════════════════════════════════════════════════════════
    //  GET /infografis-intelijen/{analisis}
    //  Detail infografis untuk satu analisis
    // ═══════════════════════════════════════════════════════════
    public function show(AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'riskItems', 'confidence', 'folder']);

        // Decode PESTLE
        $pestle  = [];
        $decoded = $this->decodeJson($analisis->analisis_intelijen);
        if (isset($decoded['politik'])) {
            $pestle = array_map(
                fn($v) => is_array($v) ? ($v['isi'] ?? $v['narasi'] ?? '') : $v,
                $decoded
            );
        }

        // Confidence untuk radar chart
        $confidence = $analisis->confidence->first();
        $radarData  = $confidence ? [
            'Kelengkapan Data'   => $confidence->kelengkapan_data,
            'Konsistensi Sumber' => $confidence->konsistensi_sumber,
            'Kualitas Dokumen'   => $confidence->kualitas_dokumen,
            'Kedalaman Analisis' => $confidence->kedalaman_analisis,
        ] : [];

        return view('infografis-intelijen-detail', compact('analisis', 'pestle', 'radarData'));
    }

    // ─── Helper ───────────────────────────────────────────────
    private function decodeJson($value): array
    {
        if (!$value) return [];
        return is_array($value) ? $value : (json_decode($value, true) ?? []);
    }
}