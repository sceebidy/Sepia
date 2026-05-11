<?php

namespace App\Http\Controllers;

use App\Models\AnalisisKasus;
use App\Models\Folder;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * GET /datapool/{folder}/laporan/{analisis}/buat
     */
    public function store(Folder $folder, AnalisisKasus $analisis)
    {
        $existing = Laporan::where('analisis_id', $analisis->id)->first();
        if ($existing) {
            return redirect()->route('laporan.show', $existing)
                ->with('success', 'Laporan sudah pernah dibuat sebelumnya.');
        }

        $laporan = Laporan::create([
            'folder_id'          => $folder->id,
            'analisis_id'        => $analisis->id,
            'judul'              => 'Laporan Analisis: ' . $folder->nama,
            'tingkat_risiko'     => $analisis->tingkat_risiko,
            'prediksi_vonis'     => $analisis->prediksi_vonis,
            'jumlah_sumber'      => $analisis->jumlah_sumber,
            'jumlah_aktor'       => DB::table('aktor_kasus')->where('analisis_id', $analisis->id)->count(),
            'jumlah_rekomendasi' => DB::table('rekomendasi_kasus')->where('analisis_id', $analisis->id)->count(),
            'dibuat_oleh'        => 'C. Rasyid',
        ]);

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * GET /laporan/{laporan}
     */
    public function show(Laporan $laporan)
{
    $laporan->load([
        'folder',
        'analisis.swotItems',
        'analisis.aktor',
    ]);

    $analisis = $laporan->analisis;

    $timeline    = $analisis ? DB::table('timeline_kasus')   ->where('analisis_id', $analisis->id)->orderBy('urutan')->get() : collect();
$rekomendasi = $analisis ? DB::table('rekomendasi_kasus')->where('analisis_id', $analisis->id)->orderBy('urutan')->get() : collect();
$riskItems   = $analisis ? DB::table('risk_assessment')  ->where('analisis_id', $analisis->id)->orderBy('urutan')->get() : collect();
$confidence  = $analisis ? DB::table('confidence_kasus') ->where('analisis_id', $analisis->id)->first() : null;

    return view('laporan-detail', compact(
        'laporan', 'timeline', 'rekomendasi', 'riskItems', 'confidence'
    ));
}

    /**
     * GET /laporan
     */
    public function index()
    {
        $laporanList = Laporan::with('folder')
            ->latest()
            ->paginate(20);

        return view('laporan-index', compact('laporanList'));
    }
}