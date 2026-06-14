<?php

namespace App\Http\Controllers;

use App\Models\AktorKasus;
use App\Models\AnalisisKasus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfilingSubjekController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  GET /profiling-subjek
    // ═══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $query = AktorKasus::with(['analisis' => fn($q) => $q->select('id', 'judul', 'tingkat_risiko', 'tanggal_analisis')])
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by peran
        if ($request->filled('peran')) {
            $query->where('peran', 'like', '%' . $request->peran . '%');
        }

        // Search by nama
        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        $aktorList = $query->paginate(16)->withQueryString();

        // Statistik
        $stats = [
            'total'     => AktorKasus::count(),
            'tersangka' => AktorKasus::where('status', 'tersangka')->count(),
            'dpo'       => AktorKasus::where('status', 'dpo')->count(),
            'saksi'     => AktorKasus::where('status', 'saksi')->count(),
        ];

        // Warna badge per status
        $statusWarna = [
            'tersangka' => '#dc2626',
            'dpo'       => '#7c3aed',
            'korban'    => '#2563eb',
            'pejabat'   => '#0891b2',
            'saksi'     => '#16a34a',
        ];

        return view('profiling-subjek', compact('aktorList', 'stats', 'statusWarna'));
    }

    // ═══════════════════════════════════════════════════════════
    //  GET /profiling-subjek/{aktor}
    // ═══════════════════════════════════════════════════════════
    public function show(AktorKasus $aktor)
    {
        $aktor->load(['analisis' => fn($q) =>
            $q->with(['riskItems', 'folder'])->orderByDesc('tanggal_analisis')
        ]);

        // Analisis lain yang menyebut aktor ini (by nama)
        $analisisTerkait = AnalisisKasus::with('folder')
            ->whereHas('aktor', fn($q) => $q->where('nama', $aktor->nama))
            ->orderByDesc('tanggal_analisis')
            ->get();

        return view('profiling-subjek-detail', compact('aktor', 'analisisTerkait'));
    }

    // ═══════════════════════════════════════════════════════════
    //  PATCH /profiling-subjek/{aktor}/status
    // ═══════════════════════════════════════════════════════════
    public function updateStatus(Request $request, AktorKasus $aktor)
    {
        $request->validate([
            'status' => 'required|in:tersangka,saksi,dpo,korban,pejabat,tidak_diketahui',
        ]);

        try {
            $aktor->update(['status' => $request->status]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('[SEPIA] Update status aktor error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}