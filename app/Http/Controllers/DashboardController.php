<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tokoh;
use App\Models\Laporan;
use App\Models\AnalisisKasus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── DATA ACUAN UTAMA (Hanya ambil analisis yang foldernya MEMANG masih ada)
        $activeAnalisisIds = AnalisisKasus::whereHas('folder')->pluck('id')->toArray();

        // ── STAT CARDS
        // FIX: Hanya hitung isu aktif yang folders-nya masih ada
        $totalIsu      = Issue::aktif()->whereHas('folder')->count();
        $risikoTinggi  = Issue::aktif()->whereHas('folder')->risiko('tinggi')->count();
        $totalTokoh    = Tokoh::aktif()->count();

        // FIX: Hitung analisis yang foldernya aktif
        $totalAnalisis = count($activeAnalisisIds);

        // FIX: Hitung laporan menggunakan pencarian string "(#ID)" berdasarkan analisis aktif
        $totalLaporan = 0;
        if (!empty($activeAnalisisIds)) {
            $totalLaporan = Laporan::where(function($query) use ($activeAnalisisIds) {
                foreach ($activeAnalisisIds as $id) {
                    $query->orWhere('judul', 'LIKE', "%(#{$id})");
                }
            })->count();
        }

        // ── DATA PER KATEGORI
        $kategoriList = ['ideologi', 'politik', 'ekonomi', 'sosbud', 'hankam'];
        $kategoriData = [];

        foreach ($kategoriList as $kat) {
            // FIX: Tambahkan whereHas('folder')
            $totalKat = Issue::aktif()->whereHas('folder')->kategori($kat)->count();

            // FIX: Tambahkan whereHas('folder')
            $subKatCounts = Issue::aktif()
                ->whereHas('folder')
                ->kategori($kat)
                ->select('sub_kategori', DB::raw('count(*) as total'))
                ->groupBy('sub_kategori')
                ->pluck('total', 'sub_kategori')
                ->toArray();

            $subKatPersentase = [];
            foreach ($subKatCounts as $sub => $count) {
                $subKatPersentase[$sub] = $totalKat > 0 ? round(($count / $totalKat) * 100) : 0;
            }

            // FIX: Tambahkan whereHas('folder')
            $recentIssues = Issue::aktif()
                ->whereHas('folder')
                ->kategori($kat)
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get(['judul', 'created_at']);

            // FIX: Tambahkan whereHas('folder')
            $mingguIni = Issue::aktif()
                ->whereHas('folder')
                ->kategori($kat)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count();

            $totalAktor = Tokoh::aktif()->where('kategori', $kat)->count();

            // FIX: Tambahkan whereHas('folder')
            $risikoKat = Issue::aktif()
                ->whereHas('folder')
                ->kategori($kat)
                ->select('risiko', DB::raw('count(*) as total'))
                ->groupBy('risiko')
                ->orderByDesc('total')
                ->first();

            $kategoriData[$kat] = [
                'total'          => $totalKat,
                'minggu_ini'     => $mingguIni,
                'total_aktor'    => $totalAktor,
                'risiko_dominan' => $risikoKat ? $risikoKat->risiko : 'rendah',
                'sub_persentase' => $subKatPersentase,
                'recent'         => $recentIssues,
            ];
        }

        // ── SPARKLINE TREN 7 HARI
        $trendData = [];
        foreach ($kategoriList as $kat) {
            $trend = [];
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = Carbon::now()->subDays($i)->toDateString();
                // FIX: Tambahkan whereHas('folder') agar grafik ikut bersih/0 jika folder dihapus
                $trend[] = Issue::aktif()->whereHas('folder')->kategori($kat)->whereDate('created_at', $tanggal)->count();
            }
            $trendData[$kat] = $trend;
        }

        // ── PROFILING TOKOH PRIORITAS
        $tokohPrioritas = Tokoh::aktif()
            ->risiko('tinggi')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get(['nama', 'inisial', 'kategori', 'peran', 'wilayah', 'risiko']);

        // ── RIWAYAT LAPORAN (dari RPI)
        if (!empty($activeAnalisisIds)) {
            $riwayatLaporan = Laporan::where(function($query) use ($activeAnalisisIds) {
                foreach ($activeAnalisisIds as $id) {
                    $query->orWhere('judul', 'LIKE', "%(#{$id})");
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        } else {
            $riwayatLaporan = collect();
        }

        // ── GRAFIK ANALISIS RPI (7 hari terakhir)
        $analisisTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->toDateString();
            $analisisTrend[] = AnalisisKasus::whereHas('folder')
                ->whereDate('created_at', $tanggal)
                ->count();
        }

        // ── STAT RISIKO DARI RPI
        $rpiStats = [
            'total'         => AnalisisKasus::whereHas('folder')->count(),
            'risiko_tinggi' => AnalisisKasus::whereHas('folder')->where('tingkat_risiko', '>=', 7)->count(),
            'risiko_sedang' => AnalisisKasus::whereHas('folder')->whereBetween('tingkat_risiko', [4, 6.9])->count(),
            'risiko_rendah' => AnalisisKasus::whereHas('folder')->where('tingkat_risiko', '<', 4)->count(),
        ];

        return view('dashboard', compact(
            'totalIsu',
            'risikoTinggi',
            'totalLaporan',
            'totalAnalisis',
            'totalTokoh',
            'kategoriData',
            'trendData',
            'tokohPrioritas',
            'riwayatLaporan',
            'analisisTrend',
            'rpiStats',
        ));
    }
}