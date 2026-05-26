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
        // Cek laporan sudah ada berdasarkan analisis_id
        $existing = Laporan::where('analisis_id', $analisis->id)->first();
        if ($existing) {
            return redirect()->route('laporan.show', $existing)
                ->with('success', 'Laporan sudah pernah dibuat sebelumnya.');
        }

        $laporan = Laporan::create([
            'judul'             => 'Laporan Analisis: ' . $folder->nama,
            'ringkasan'         => $analisis->ringkasan_eksekutif,
            'kategori'          => 'umum',
            'status'            => 'draft',
            'dibuat_oleh'       => 'Analis SEPIA',
            'analisis_id'       => $analisis->id,
            'folder_id'         => $folder->id,
            'tingkat_risiko'    => $analisis->tingkat_risiko,
            'prediksi_vonis'    => $analisis->prediksi_vonis,
            'jumlah_sumber'     => $analisis->jumlah_sumber,
            'jumlah_aktor'      => $analisis->aktor()->count(),
            'jumlah_rekomendasi'=> \DB::table('rekomendasi_kasus')->where('analisis_id', $analisis->id)->count(),     
   ]);

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * GET /laporan/{laporan}
     */
    public function show(Laporan $laporan)
    {
        // Ambil analisis langsung dari relasi analisis_id
        $analisis = $laporan->analisis_id
            ? AnalisisKasus::with(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'confidence', 'riskItems'])
                ->find($laporan->analisis_id)
            : null;

        $timeline    = $analisis ? $analisis->timeline()->orderBy('urutan')->get()    : collect();
        $rekomendasi = $analisis ? $analisis->rekomendasi()->orderBy('urutan')->get() : collect();
        $riskItems   = $analisis ? $analisis->riskItems()->orderBy('urutan')->get()   : collect();
        $confidence  = $analisis ? $analisis->confidence()->first()                   : null;

        return view('laporan-detail', compact(
            'laporan', 'analisis', 'timeline', 'rekomendasi', 'riskItems', 'confidence'
        ));
    }

    /**
     * GET /laporan
     */
    public function index()
    {
        $laporanList = Laporan::latest()->paginate(20);
        return view('laporan-index', compact('laporanList'));
    }
}