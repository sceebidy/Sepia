<?php

namespace App\Http\Controllers;

use App\Models\AnalisisKasus;
use App\Models\Issue;
use Illuminate\Http\Request;

class PenjabaranStrategisController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  GET /penjabaran-strategis
    // ═══════════════════════════════════════════════════════════
    public function index()
    {
        // Ambil semua analisis yang sudah punya ringkasan eksekutif
        $analisisList = AnalisisKasus::with(['riskItems', 'rekomendasi'])
            ->whereNotNull('ringkasan_eksekutif')
            ->orderByDesc('tanggal_analisis')
            ->get();

        // Statistik untuk summary card
        $stats = [
            'total_analisis'  => $analisisList->count(),
            'risiko_tinggi'   => $analisisList->where('tingkat_risiko', '>=', 7)->count(),
            'risiko_sedang'   => $analisisList->whereBetween('tingkat_risiko', [4, 6])->count(),
            'risiko_rendah'   => $analisisList->where('tingkat_risiko', '<', 4)->count(),
        ];

        // Issue aktif per kategori untuk peta strategis
        $issuePerKategori = Issue::where('status', 'aktif')
            ->selectRaw('kategori, COUNT(*) as jumlah')
            ->groupBy('kategori')
            ->pluck('jumlah', 'kategori')
            ->toArray();

        return view('penjabaran-strategis', compact('analisisList', 'stats', 'issuePerKategori'));
    }

    // ═══════════════════════════════════════════════════════════
    //  GET /penjabaran-strategis/{analisis}
    // ═══════════════════════════════════════════════════════════
    public function show(AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'confidence', 'riskItems', 'folder']);

        // Decode PESTLE
        $pestle = [];
        if ($analisis->analisis_intelijen) {
            $decoded = is_array($analisis->analisis_intelijen)
                ? $analisis->analisis_intelijen
                : json_decode($analisis->analisis_intelijen, true);

            if (is_array($decoded) && isset($decoded['politik'])) {
                $pestle = array_map(
                    fn($v) => is_array($v) ? ($v['isi'] ?? $v['narasi'] ?? '') : $v,
                    $decoded
                );
            }
        }

        // Decode fakta
        $fakta = [];
        if ($analisis->fakta_fakta) {
            $fakta = is_array($analisis->fakta_fakta)
                ? $analisis->fakta_fakta
                : json_decode($analisis->fakta_fakta, true) ?? [];
        }

        // Decode early warning
        $earlyWarning = [];
        if ($analisis->early_warning) {
            $earlyWarning = is_array($analisis->early_warning)
                ? $analisis->early_warning
                : json_decode($analisis->early_warning, true) ?? [];
        }

        return view('penjabaran-strategis-detail', compact('analisis', 'pestle', 'fakta', 'earlyWarning'));
    }
}