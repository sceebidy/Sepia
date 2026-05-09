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
use Illuminate\Support\Facades\Http;

class AnalisisController extends Controller
{
    // ── Trigger analisis ke n8n (POST) - Opsi B: Loading + Auto Redirect
    public function store(Request $request, Folder $folder)
    {
        // Validasi folder punya items
        $items = $folder->items()->get();
        if ($items->isEmpty()) {
            return response()->json(['error' => 'Folder tidak ada sumber data'], 400);
        }

        try {
            // Buat record analisis kosong dulu (sebagai placeholder)
            $analisis = AnalisisKasus::updateOrCreate(
                ['folder_id' => $folder->id],
                [
                    'judul'            => $folder->nama,
                    'tanggal_analisis' => now(),
                    'tingkat_risiko'   => 0,
                    'jumlah_sumber'    => $items->count(),
                    'model_versi'      => 'SEPIA v1.0 (AI)',
                ]
            );

            // Kirim ke n8n webhook
            $this->sendToN8n($folder, $items, $analisis);

            return response()->json([
                'success' => true,
                'analisis_id' => $analisis->id,
                'folder_id' => $folder->id,
                'message' => 'Analisis dimulai, tunggu hasil...'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ── Kirim data ke n8n webhook
    private function sendToN8n($folder, $items, $analisis)
    {
        $n8nWebhook = env('N8N_WEBHOOK_URL');
        
        if (!$n8nWebhook) {
            throw new \Exception('N8N_WEBHOOK_URL tidak dikonfigurasi di .env');
        }

        // Siapkan payload untuk n8n
        $payload = [
            'folder_id'    => $folder->id,
            'analisis_id'  => $analisis->id,
            'folder_nama'  => $folder->nama,
            'items'        => $items->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'tipe'       => $item->tipe,
                    'judul'      => $item->judul,
                    'konten'     => $item->konten,
                    'file_path'  => $item->file_path,
                    'file_nama'  => $item->file_nama,
                ];
            })->toArray(),
            'callback_url' => route('analisis.callback'),
        ];

        try {
            $response = Http::timeout(10)->post($n8nWebhook, $payload);
            
            if (!$response->successful()) {
                throw new \Exception('N8n webhook error: ' . $response->body());
            }
        } catch (\Exception $e) {
            throw new \Exception('Gagal terhubung ke n8n: ' . $e->getMessage());
        }
    }

    // ── Callback dari n8n - Terima hasil analisis
    public function callback(Request $request)
    {
        try {
            $data = $request->validate([
                'analisis_id'       => 'required|integer',
                'swot'              => 'required|array',
                'aktor'             => 'required|array',
                'timeline'          => 'required|array',
                'rekomendasi'       => 'required|array',
                'risk'              => 'required|array',
                'confidence'        => 'required|array',
                'tingkat_risiko'    => 'required|numeric',
                'prediksi_vonis'    => 'nullable|string',
            ]);

            $analisis = AnalisisKasus::findOrFail($data['analisis_id']);

            // Hapus data lama kalau ada (untuk update)
            SwotItem::where('analisis_id', $analisis->id)->delete();
            AktorKasus::where('analisis_id', $analisis->id)->delete();
            TimelineKasus::where('analisis_id', $analisis->id)->delete();
            RekomendasiKasus::where('analisis_id', $analisis->id)->delete();
            RiskAssessment::where('analisis_id', $analisis->id)->delete();
            ConfidenceKasus::where('analisis_id', $analisis->id)->delete();

            // Update header analisis
            $analisis->update([
                'tingkat_risiko' => $data['tingkat_risiko'],
                'prediksi_vonis' => $data['prediksi_vonis'],
            ]);

            // ── Simpan SWOT
            foreach ($data['swot'] as $tipe => $items) {
                foreach ($items as $i => $isi) {
                    if (trim($isi)) {
                        SwotItem::create([
                            'analisis_id' => $analisis->id,
                            'tipe'        => $tipe,
                            'isi'         => $isi,
                            'urutan'      => $i,
                        ]);
                    }
                }
            }

            // ── Simpan Aktor
            foreach ($data['aktor'] as $i => $aktor) {
                if (isset($aktor['nama']) && trim($aktor['nama'])) {
                    AktorKasus::create([
                        'analisis_id'  => $analisis->id,
                        'nama'         => $aktor['nama'],
                        'inisial'      => $aktor['inisial'] ?? '?',
                        'peran'        => $aktor['peran'] ?? '-',
                        'status'       => $aktor['status'] ?? 'saksi',
                        'warna_avatar' => $aktor['warna_avatar'] ?? '#1a5c2e',
                    ]);
                }
            }

            // ── Simpan Timeline
            foreach ($data['timeline'] as $i => $tl) {
                if (isset($tl['tanggal']) && trim($tl['tanggal'])) {
                    TimelineKasus::create([
                        'analisis_id' => $analisis->id,
                        'tanggal'     => $tl['tanggal'],
                        'keterangan'  => $tl['keterangan'] ?? '-',
                        'warna_dot'   => $tl['warna_dot'] ?? '#16a34a',
                        'urutan'      => $i,
                    ]);
                }
            }

            // ── Simpan Rekomendasi
            foreach ($data['rekomendasi'] as $i => $reko) {
                if (isset($reko['judul']) && trim($reko['judul'])) {
                    RekomendasiKasus::create([
                        'analisis_id' => $analisis->id,
                        'judul'       => $reko['judul'],
                        'deskripsi'   => $reko['deskripsi'] ?? '-',
                        'prioritas'   => $reko['prioritas'] ?? 'sedang',
                        'urutan'      => $i,
                    ]);
                }
            }

            // ── Simpan Risk Assessment
            foreach ($data['risk'] as $i => $risk) {
                if (isset($risk['label']) && trim($risk['label'])) {
                    RiskAssessment::create([
                        'analisis_id' => $analisis->id,
                        'label'       => $risk['label'],
                        'nilai'       => $risk['nilai'] ?? 0,
                        'warna'       => $risk['warna'] ?? '#dc2626',
                        'keterangan'  => $risk['keterangan'] ?? null,
                        'urutan'      => $i,
                    ]);
                }
            }

            // ── Simpan Confidence
            ConfidenceKasus::create([
                'analisis_id'        => $analisis->id,
                'kelengkapan_data'   => $data['confidence']['kelengkapan_data'] ?? 0,
                'konsistensi_sumber' => $data['confidence']['konsistensi_sumber'] ?? 0,
                'kualitas_dokumen'   => $data['confidence']['kualitas_dokumen'] ?? 0,
                'kedalaman_analisis' => $data['confidence']['kedalaman_analisis'] ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'analisis_id' => $analisis->id,
                'message' => 'Analisis berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            \Log::error('Callback error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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