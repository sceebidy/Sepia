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
        // ── STAT CARDS
        $totalIsu      = Issue::aktif()->count();
        $risikoTinggi  = Issue::aktif()->risiko('tinggi')->count();
        $totalLaporan  = Laporan::whereNotNull('analisis_id')->count() + Laporan::whereNull('analisis_id')->count();
        $totalAnalisis = AnalisisKasus::count();
        $totalTokoh    = Tokoh::aktif()->count();

        // ── DATA PER KATEGORI
        $kategoriList = ['ideologi', 'politik', 'ekonomi', 'sosbud', 'hankam'];
        $kategoriData = [];

        foreach ($kategoriList as $kat) {
            $totalKat = Issue::aktif()->kategori($kat)->count();

            $subKatCounts = Issue::aktif()
                ->kategori($kat)
                ->select('sub_kategori', DB::raw('count(*) as total'))
                ->groupBy('sub_kategori')
                ->pluck('total', 'sub_kategori')
                ->toArray();

            $subKatPersentase = [];
            foreach ($subKatCounts as $sub => $count) {
                $subKatPersentase[$sub] = $totalKat > 0 ? round(($count / $totalKat) * 100) : 0;
            }

            $recentIssues = Issue::aktif()
                ->kategori($kat)
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get(['judul', 'created_at']);

            $mingguIni = Issue::aktif()
                ->kategori($kat)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count();

            $totalAktor = Tokoh::aktif()->where('kategori', $kat)->count();

            $risikoKat = Issue::aktif()
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
                $trend[] = Issue::aktif()->kategori($kat)->whereDate('created_at', $tanggal)->count();
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
        $riwayatLaporan = Laporan::with('folder')
            ->whereNotNull('analisis_id')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ── GRAFIK ANALISIS RPI (7 hari terakhir)
        $analisisTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->toDateString();
            $analisisTrend[] = AnalisisKasus::whereDate('created_at', $tanggal)->count();
        }

        // ── STAT RISIKO DARI RPI
        $rpiStats = [
            'total'        => AnalisisKasus::count(),
            'risiko_tinggi' => AnalisisKasus::where('tingkat_risiko', '>=', 7)->count(),
            'risiko_sedang' => AnalisisKasus::whereBetween('tingkat_risiko', [4, 6.9])->count(),
            'risiko_rendah' => AnalisisKasus::where('tingkat_risiko', '<', 4)->count(),
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