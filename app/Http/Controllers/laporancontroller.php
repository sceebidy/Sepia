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
        // Berhubung tabel laporan tidak punya foreign key, kita gunakan pencarian kecocokan judul 
        // untuk mencegah duplikasi laporan dari analisis kasus yang sama di folder ini.
        $judulLaporan = 'Laporan Analisis: ' . $folder->nama . ' (#' . $analisis->id . ')';

        $existing = Laporan::where('judul', $judulLaporan)->first();
        if ($existing) {
            return redirect()->route('laporan.show', $existing)
                ->with('success', 'Laporan sudah pernah dibuat sebelumnya.');
        }

        // Ambil kategori dari folder/analisis (fallback ke 'umum' jika tidak cocok dengan enum laporan)
        // Enum laporan: 'ideologi','politik','ekonomi','sosbud','hankam','umum'
        $kategori = 'umum'; 

        $laporan = Laporan::create([
            'judul'       => $judulLaporan,
            'ringkasan'   => $analisis->ringkasan_eksekutif,
            'kategori'    => $kategori,
            'status'      => 'draft',
            'dibuat_oleh' => 'C. Rasyid',
        ]);

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * GET /laporan/{laporan}
     */
    public function show(Laporan $laporan)
    {
        // Ekstrak ID analisis dari judul laporan menggunakan regex (Sesuai Solusi 1)
        $analisis = null;
        if (preg_match('/\(#(\d+)\)$/', $laporan->judul, $matches)) {
            $analisis = AnalisisKasus::find($matches[1]);
        }

        // Mengambil data-data pendukung intelijen dari analisis terkait jika ditemukan
        $timeline    = $analisis ? DB::table('timeline_kasus')   ->where('analisis_id', $analisis->id)->orderBy('urutan')->get() : collect();
        $rekomendasi = $analisis ? DB::table('rekomendasi_kasus')->where('analisis_id', $analisis->id)->orderBy('urutan')->get() : collect();
        $riskItems   = $analisis ? DB::table('risk_assessment')  ->where('analisis_id', $analisis->id)->orderBy('urutan')->get() : collect();
        $confidence  = $analisis ? DB::table('confidence_kasus') ->where('analisis_id', $analisis->id)->first() : null;

        // PASTIKAN '$analisis' masuk ke dalam compact() agar bisa dibaca oleh file blade
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