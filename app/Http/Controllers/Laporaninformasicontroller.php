<?php

namespace App\Http\Controllers;

use App\Models\AnalisisKasus;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanInformasiController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  GET /laporan-informasi
    // ═══════════════════════════════════════════════════════════
    public function index()
    {
        // Semua folder yang sudah punya analisis (sumber data RPI)
        $folders = Folder::with(['analisis' => function ($q) {
                $q->with(['riskItems'])->orderByDesc('tanggal_analisis');
            }])
            ->whereHas('analisis')
            ->orderByDesc('updated_at')
            ->get();

        // Statistik ringkas
        $stats = [
            'total_folder'   => $folders->count(),
            'total_analisis' => AnalisisKasus::count(),
            'bulan_ini'      => AnalisisKasus::whereMonth('tanggal_analisis', now()->month)
                                    ->whereYear('tanggal_analisis', now()->year)
                                    ->count(),
        ];

        return view('laporan-informasi', compact('folders', 'stats'));
    }

    // ═══════════════════════════════════════════════════════════
    //  GET /laporan-informasi/{analisis}
    // ═══════════════════════════════════════════════════════════
    public function show(AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'confidence', 'riskItems', 'folder']);

        // Decode fields JSON
        $fakta        = $this->decodeJson($analisis->fakta_fakta);
        $earlyWarning = $this->decodeJson($analisis->early_warning);
        $jabatanRek   = $this->decodeJson($analisis->jabatan_rekomendasi);

        // Decode PESTLE dengan normalisasi
        $pestle = [];
        $decoded = $this->decodeJson($analisis->analisis_intelijen);
        if (is_array($decoded) && isset($decoded['politik'])) {
            $pestle = array_map(
                fn($v) => is_array($v) ? ($v['isi'] ?? $v['narasi'] ?? '') : $v,
                $decoded
            );
        }

        return view('laporan-informasi-detail', compact(
            'analisis', 'fakta', 'earlyWarning', 'jabatanRek', 'pestle'
        ));
    }

    // ═══════════════════════════════════════════════════════════
    //  POST /laporan-informasi/{analisis}/export
    // ═══════════════════════════════════════════════════════════
    public function export(Request $request, AnalisisKasus $analisis)
    {
        // Placeholder — bisa diextend ke PDF/DOCX export
        try {
            $format = $request->input('format', 'pdf');
            Log::info('[SEPIA] Export laporan informasi: ' . $analisis->id . ' format: ' . $format);

            return response()->json([
                'success' => true,
                'message' => 'Export akan diproses',
                'format'  => $format,
            ]);
        } catch (\Exception $e) {
            Log::error('[SEPIA] Export error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─── Helper ───────────────────────────────────────────────
    private function decodeJson($value): array
    {
        if (!$value) return [];
        return is_array($value) ? $value : (json_decode($value, true) ?? []);
    }
}