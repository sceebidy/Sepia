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
        // FIX AMAN: Karena kolom 'analisis_id' tidak ada di tabel 'laporan', 
        // kita cari data existing menggunakan teks unik/pola ID pada kolom 'judul'
        $patternJudul = 'Laporan Analisis: ' . $folder->nama . ' (#' . $analisis->id . ')';
        $existing = Laporan::where('judul', $patternJudul)->first();
        
        if ($existing) {
            return redirect()->route('laporan.show', $existing)
                ->with('success', 'Laporan sudah pernah dibuat sebelumnya.');
        }

        // Kumpulkan data ke dalam array terlebih dahulu
        $dataLaporan = [
            'judul'             => $patternJudul, // Menyimpan ID di dalam judul sebagai penanda
            'ringkasan'         => $analisis->ringkasan_eksekutif,
            'kategori'          => 'umum',
            'status'            => 'draft',
            'dibuat_oleh'       => 'Analis SEPIA',
            'folder_id'         => $folder->id,
            'tingkat_risiko'    => $analisis->tingkat_risiko,
            'prediksi_vonis'    => $analisis->prediksi_vonis,
            'jumlah_sumber'     => $analisis->jumlah_sumber,
            'jumlah_aktor'      => $analisis->aktor()->count(),
            'jumlah_rekomendasi'=> DB::table('rekomendasi_kasus')->where('analisis_id', $analisis->id)->count(),
        ];

        // OPTIONAL CHECK: Jika di masa depan Anda melakukan migrasi database dan menambahkan 'analisis_id',
        // kode di bawah ini akan otomatis mengisinya tanpa merusak sistem sekarang
        if (DB::getSchemaBuilder()->hasColumn('laporan', 'analisis_id')) {
            $dataLaporan['analisis_id'] = $analisis->id;
        }

        $laporan = Laporan::create($dataLaporan);

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * GET /laporan/{laporan}
     */
    public function show(Laporan $laporan)
    {
        $analisisId = null;

        // Cek jika kolom analisis_id tersedia dan terisi
        if (DB::getSchemaBuilder()->hasColumn('laporan', 'analisis_id') && $laporan->analisis_id) {
            $analisisId = $laporan->analisis_id;
        } else {
            // FALLBACK: Jika kolom tidak ada, ekstrak ID analisis dari string judul "(#ID)"
            preg_match('/\(#(\d+)\)$/', $laporan->judul, $matches);
            if (!empty($matches[1])) {
                $analisisId = $matches[1];
            }
        }

        // Ambil data analisis menggunakan ID yang berhasil diekstrak
        $analisis = $analisisId
            ? AnalisisKasus::with(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'confidence', 'riskItems'])->find($analisisId)
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