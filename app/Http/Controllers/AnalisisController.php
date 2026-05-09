<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\AnalisisKasus;
use App\Models\SwotItem;
use App\Models\AktorKasus;
use App\Models\TimelineKasus;
use App\Models\RekomendasiKasus;
use App\Models\ConfidenceKasus;
use App\Models\RiskAssessment;
use Illuminate\Http\Request;

class AnalisisController extends Controller
{
    // ── Form input analisis (GET)
    public function create(Folder $folder)
    {
        // cek apakah sudah ada analisis untuk folder ini
        $existing = AnalisisKasus::where('folder_id', $folder->id)->first();
        return view('analisis-form', compact('folder', 'existing'));
    }

    // ── Simpan analisis baru (POST)
    public function store(Request $request, Folder $folder)
    {
        $request->validate([
            'judul'           => 'required|string|max:255',
            'tingkat_risiko'  => 'required|numeric|min:0|max:10',
            'prediksi_vonis'  => 'nullable|string|max:100',
            'jumlah_sumber'   => 'required|integer|min:0',
        ]);

        // ── Hapus analisis lama kalau ada
        AnalisisKasus::where('folder_id', $folder->id)->delete();

        // ── Buat analisis baru
        $analisis = AnalisisKasus::create([
            'folder_id'        => $folder->id,
            'judul'            => $request->judul,
            'tanggal_analisis' => now(),
            'tingkat_risiko'   => $request->tingkat_risiko,
            'prediksi_vonis'   => $request->prediksi_vonis,
            'jumlah_sumber'    => $request->jumlah_sumber,
            'model_versi'      => 'SEPIA v1.0',
        ]);

        // ── SWOT Items
        $swotTipes = ['S', 'W', 'O', 'T'];
        foreach ($swotTipes as $tipe) {
            $items = $request->input('swot_' . strtolower($tipe), []);
            foreach (array_filter($items) as $i => $isi) {
                SwotItem::create([
                    'analisis_id' => $analisis->id,
                    'tipe'        => $tipe,
                    'isi'         => $isi,
                    'urutan'      => $i,
                ]);
            }
        }

        // ── Aktor
        $aktorNama   = $request->input('aktor_nama', []);
        $aktorInisial= $request->input('aktor_inisial', []);
        $aktorPeran  = $request->input('aktor_peran', []);
        $aktorStatus = $request->input('aktor_status', []);
        $aktorWarna  = $request->input('aktor_warna', []);
        foreach ($aktorNama as $i => $nama) {
            if (!$nama) continue;
            AktorKasus::create([
                'analisis_id'  => $analisis->id,
                'nama'         => $nama,
                'inisial'      => $aktorInisial[$i] ?? '?',
                'peran'        => $aktorPeran[$i] ?? '-',
                'status'       => $aktorStatus[$i] ?? 'saksi',
                'warna_avatar' => $aktorWarna[$i] ?? '#1a5c2e',
            ]);
        }

        // ── Timeline
        $tlTanggal    = $request->input('tl_tanggal', []);
        $tlKeterangan = $request->input('tl_keterangan', []);
        $tlWarna      = $request->input('tl_warna', []);
        foreach ($tlTanggal as $i => $tanggal) {
            if (!$tanggal) continue;
            TimelineKasus::create([
                'analisis_id' => $analisis->id,
                'tanggal'     => $tanggal,
                'keterangan'  => $tlKeterangan[$i] ?? '-',
                'warna_dot'   => $tlWarna[$i] ?? '#16a34a',
                'urutan'      => $i,
            ]);
        }

        // ── Rekomendasi
        $rekoJudul    = $request->input('reko_judul', []);
        $rekoDeskripsi= $request->input('reko_deskripsi', []);
        $rekoPrioritas= $request->input('reko_prioritas', []);
        foreach ($rekoJudul as $i => $judul) {
            if (!$judul) continue;
            RekomendasiKasus::create([
                'analisis_id' => $analisis->id,
                'judul'       => $judul,
                'deskripsi'   => $rekoDeskripsi[$i] ?? '-',
                'prioritas'   => $rekoPrioritas[$i] ?? 'sedang',
                'urutan'      => $i,
            ]);
        }

        // ── Confidence
        ConfidenceKasus::create([
            'analisis_id'        => $analisis->id,
            'kelengkapan_data'   => $request->input('conf_kelengkapan', 0),
            'konsistensi_sumber' => $request->input('conf_konsistensi', 0),
            'kualitas_dokumen'   => $request->input('conf_kualitas', 0),
            'kedalaman_analisis' => $request->input('conf_kedalaman', 0),
        ]);

        // ── Risk Assessment
        $riskLabel     = $request->input('risk_label', []);
        $riskNilai     = $request->input('risk_nilai', []);
        $riskWarna     = $request->input('risk_warna', []);
        $riskKeterangan= $request->input('risk_keterangan', []);
        foreach ($riskLabel as $i => $label) {
            if (!$label) continue;
            RiskAssessment::create([
                'analisis_id' => $analisis->id,
                'label'       => $label,
                'nilai'       => $riskNilai[$i] ?? 0,
                'warna'       => $riskWarna[$i] ?? '#dc2626',
                'keterangan'  => $riskKeterangan[$i] ?? null,
                'urutan'      => $i,
            ]);
        }

        return redirect()->route('analisis.show', [$folder, $analisis])
            ->with('success', 'Analisis berhasil disimpan.');
    }

    // ── Tampilkan hasil analisis (GET)
    public function show(Folder $folder, AnalisisKasus $analisis)
    {
        $analisis->load([
            'swotItems',
            'aktor',
            'timeline',
            'rekomendasi',
            'confidence',
            'riskItems',
        ]);

        return view('analisis-hasil', compact('folder', 'analisis'));
    }
}