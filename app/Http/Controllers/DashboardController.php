<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tokoh;
use App\Models\Laporan;
use App\Models\Analisis;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ══════════════════════════════════════
        // STAT CARDS
        // ══════════════════════════════════════

        $totalIsu      = Issue::aktif()->count();
        $risikoTinggi  = Issue::aktif()->risiko('tinggi')->count();
        $totalLaporan  = Laporan::count();
        $totalAnalisis = Analisis::count();
        $totalTokoh    = Tokoh::aktif()->count();

        // ══════════════════════════════════════
        // DATA PER KATEGORI (donut + bar chart)
        // ══════════════════════════════════════

        $kategoriList = ['ideologi', 'politik', 'ekonomi', 'sosbud', 'hankam'];

        $kategoriData = [];

        foreach ($kategoriList as $kat) {
            $totalKat = Issue::aktif()->kategori($kat)->count();

            // sub kategori count
            $subKatCounts = Issue::aktif()
                ->kategori($kat)
                ->select('sub_kategori', DB::raw('count(*) as total'))
                ->groupBy('sub_kategori')
                ->pluck('total', 'sub_kategori')
                ->toArray();

            // hitung persentase per sub kategori
            $subKatPersentase = [];
            foreach ($subKatCounts as $sub => $count) {
                $subKatPersentase[$sub] = $totalKat > 0
                    ? round(($count / $totalKat) * 100)
                    : 0;
            }

            // risiko breakdown
            $risikoBreakdown = Issue::aktif()
                ->kategori($kat)
                ->select('risiko', DB::raw('count(*) as total'))
                ->groupBy('risiko')
                ->pluck('total', 'risiko')
                ->toArray();

            // 2 isu terbaru (recent items)
            $recentIssues = Issue::aktif()
                ->kategori($kat)
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get(['judul', 'created_at']);

            // minggu ini
            $mingguIni = Issue::aktif()
                ->kategori($kat)
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])
                ->count();

            // aktor (tokoh) terkait kategori ini
            $totalAktor = Tokoh::aktif()
                ->where('kategori', $kat)
                ->count();

            // level risiko dominan
            $risikoKat = Issue::aktif()
                ->kategori($kat)
                ->select('risiko', DB::raw('count(*) as total'))
                ->groupBy('risiko')
                ->orderByDesc('total')
                ->first();

            $kategoriData[$kat] = [
                'total'            => $totalKat,
                'minggu_ini'       => $mingguIni,
                'total_aktor'      => $totalAktor,
                'risiko_dominan'   => $risikoKat ? $risikoKat->risiko : 'rendah',
                'sub_persentase'   => $subKatPersentase,
                'risiko_breakdown' => $risikoBreakdown,
                'recent'           => $recentIssues,
            ];
        }

        // ══════════════════════════════════════
        // SPARKLINE TREN 7 HARI PER KATEGORI
        // ══════════════════════════════════════

        $trendData = [];

        foreach ($kategoriList as $kat) {
            $trend = [];
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = Carbon::now()->subDays($i)->toDateString();
                $count   = Issue::aktif()
                    ->kategori($kat)
                    ->whereDate('created_at', $tanggal)
                    ->count();
                $trend[] = $count;
            }
            $trendData[$kat] = $trend;
        }

        // ══════════════════════════════════════
        // PROFILING TOKOH PRIORITAS TINGGI
        // ══════════════════════════════════════

        $tokohPrioritas = Tokoh::aktif()
            ->risiko('tinggi')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get(['nama', 'inisial', 'kategori', 'peran', 'wilayah', 'risiko']);

        // ══════════════════════════════════════
        // KIRIM KE BLADE
        // ══════════════════════════════════════

        return view('dashboard', compact(
            'totalIsu',
            'risikoTinggi',
            'totalLaporan',
            'totalAnalisis',
            'totalTokoh',
            'kategoriData',
            'trendData',
            'tokohPrioritas',
        ));
    }
}