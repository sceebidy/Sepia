<?php

namespace App\Http\Controllers;

use App\Models\AnalisisKasus;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanInteligenController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  GET /laporan-intelijen
    // ═══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $query = AnalisisKasus::with(['riskItems', 'folder'])
            ->whereNotNull('ringkasan_eksekutif')
            ->orderByDesc('tanggal_analisis');

        // Filter kategori jika ada
        if ($request->filled('kategori')) {
            $query->whereHas('folder', fn($q) =>
                $q->where('kategori', $request->kategori)
            );
        }

        // Filter klasifikasi dokumen
        if ($request->filled('klasifikasi')) {
            $query->where('klasifikasi_dokumen', $request->klasifikasi);
        }

        // Filter tingkat risiko
        if ($request->filled('risiko')) {
            match ($request->risiko) {
                'tinggi' => $query->where('tingkat_risiko', '>=', 7),
                'sedang' => $query->whereBetween('tingkat_risiko', [4, 6]),
                'rendah' => $query->where('tingkat_risiko', '<', 4),
                default  => null,
            };
        }

        $laporanList = $query->paginate(12)->withQueryString();

        // Statistik header
        $stats = [
            'total'          => AnalisisKasus::whereNotNull('ringkasan_eksekutif')->count(),
            'rahasia'        => AnalisisKasus::where('klasifikasi_dokumen', 'RAHASIA')->count(),
            'terbatas'       => AnalisisKasus::where('klasifikasi_dokumen', 'TERBATAS')->count(),
            'biasa'          => AnalisisKasus::where('klasifikasi_dokumen', 'BIASA')->count(),
            'risiko_tinggi'  => AnalisisKasus::where('tingkat_risiko', '>=', 7)->count(),
        ];

        // Issue aktif untuk panel monitoring
        $issueAktif = Issue::where('status', 'aktif')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('laporan-intelijen', compact('laporanList', 'stats', 'issueAktif'));
    }

    // ═══════════════════════════════════════════════════════════
    //  GET /laporan-intelijen/{analisis}
    // ═══════════════════════════════════════════════════════════
    public function show(AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'confidence', 'riskItems', 'folder']);

        // Decode semua JSON field
        $fakta        = $this->decodeJson($analisis->fakta_fakta);
        $earlyWarning = $this->decodeJson($analisis->early_warning);
        $jabatanRek   = $this->decodeJson($analisis->jabatan_rekomendasi);

        // Decode PESTLE dengan normalisasi
        $pestle  = [];
        $decoded = $this->decodeJson($analisis->analisis_intelijen);
        if (isset($decoded['politik'])) {
            $pestle = array_map(
                fn($v) => is_array($v) ? ($v['isi'] ?? $v['narasi'] ?? '') : $v,
                $decoded
            );
        }

        // Label warna klasifikasi
        $klasifikasiWarna = match ($analisis->klasifikasi_dokumen) {
            'RAHASIA'  => '#dc2626',
            'TERBATAS' => '#f97316',
            'BIASA'    => '#16a34a',
            default    => '#6b7280',
        };

        return view('laporan-intelijen-detail', compact(
            'analisis', 'fakta', 'earlyWarning', 'jabatanRek', 'pestle', 'klasifikasiWarna'
        ));
    }

    // ═══════════════════════════════════════════════════════════
    //  PATCH /laporan-intelijen/{analisis}/klasifikasi
    // ═══════════════════════════════════════════════════════════
    public function updateKlasifikasi(Request $request, AnalisisKasus $analisis)
    {
        $request->validate([
            'klasifikasi_dokumen' => 'required|in:RAHASIA,TERBATAS,BIASA',
        ]);

        try {
            $analisis->update([
                'klasifikasi_dokumen' => $request->klasifikasi_dokumen,
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('[SEPIA] Update klasifikasi error: ' . $e->getMessage());
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