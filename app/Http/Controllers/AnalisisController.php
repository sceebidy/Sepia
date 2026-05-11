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
    public function store(Request $request, Folder $folder)
    {
        $items = $folder->items()->get();
        if ($items->isEmpty()) {
            return response()->json(['error' => 'Folder tidak ada sumber data'], 400);
        }

        try {
            // Hapus analisis lama beserta relasinya
            $existing = AnalisisKasus::where('folder_id', $folder->id)->first();
            if ($existing) {
                SwotItem::where('analisis_id', $existing->id)->delete();
                AktorKasus::where('analisis_id', $existing->id)->delete();
                TimelineKasus::where('analisis_id', $existing->id)->delete();
                RekomendasiKasus::where('analisis_id', $existing->id)->delete();
                RiskAssessment::where('analisis_id', $existing->id)->delete();
                ConfidenceKasus::where('analisis_id', $existing->id)->delete();
                $existing->delete();
            }

            $analisis = AnalisisKasus::create([
                'folder_id'        => $folder->id,
                'judul'            => $folder->nama,
                'tanggal_analisis' => now(),
                'tingkat_risiko'   => 0,
                'jumlah_sumber'    => $items->count(),
                'model_versi'      => 'SEPIA v1.0 (AI)',
            ]);

            $this->sendToN8n($folder, $items, $analisis);

            return response()->json([
                'success'     => true,
                'analisis_id' => $analisis->id,
                'folder_id'   => $folder->id,
                'message'     => 'Analisis dimulai, tunggu hasil...',
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function sendToN8n($folder, $items, $analisis)
    {
        $n8nWebhook = env('N8N_WEBHOOK_URL');

        if (!$n8nWebhook) {
            throw new \Exception('N8N_WEBHOOK_URL tidak dikonfigurasi di .env');
        }

        $payload = [
            'action'       => 'analisis',
            'folder_id'    => $folder->id,
            'analisis_id'  => $analisis->id,
            'folder_nama'  => $folder->nama,
            'items'        => $items->map(function ($item) {
                return [
                    'id'        => $item->id,
                    'tipe'      => $item->tipe,
                    'judul'     => $item->judul,
                    'konten'    => $item->konten,
                    'file_path' => $item->file_path,
                    'file_nama' => $item->file_nama,
                ];
            })->toArray(),
            'callback_url' => 'https://salsa-unnerving-enrich.ngrok-free.dev/analisis/callback',
        ];

        $response = Http::timeout(10)->post($n8nWebhook, $payload);

        if (!$response->successful()) {
            throw new \Exception('N8n webhook error: ' . $response->body());
        }
    }

    public function callback(Request $request)
    {
        try {
            $data = $request->validate([
                'analisis_id'    => 'required|integer',
                'swot'           => 'required|array',
                'aktor'          => 'required|array',
                'timeline'       => 'required|array',
                'rekomendasi'    => 'required|array',
                'risk'           => 'required|array',
                'confidence'     => 'required|array',
                'tingkat_risiko' => 'required|numeric',
                'prediksi_vonis' => 'nullable|string',
            ]);

            $analisis = AnalisisKasus::findOrFail($data['analisis_id']);

            SwotItem::where('analisis_id', $analisis->id)->delete();
            AktorKasus::where('analisis_id', $analisis->id)->delete();
            TimelineKasus::where('analisis_id', $analisis->id)->delete();
            RekomendasiKasus::where('analisis_id', $analisis->id)->delete();
            RiskAssessment::where('analisis_id', $analisis->id)->delete();
            ConfidenceKasus::where('analisis_id', $analisis->id)->delete();

            $analisis->update([
                'tingkat_risiko' => $data['tingkat_risiko'],
                'prediksi_vonis' => $data['prediksi_vonis'],
            ]);

            foreach ($data['swot'] as $tipe => $swotItems) {
                foreach ($swotItems as $i => $isi) {
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

            ConfidenceKasus::create([
                'analisis_id'        => $analisis->id,
                'kelengkapan_data'   => $data['confidence']['kelengkapan_data'] ?? 0,
                'konsistensi_sumber' => $data['confidence']['konsistensi_sumber'] ?? 0,
                'kualitas_dokumen'   => $data['confidence']['kualitas_dokumen'] ?? 0,
                'kedalaman_analisis' => $data['confidence']['kedalaman_analisis'] ?? 0,
            ]);

            return response()->json([
                'success'     => true,
                'analisis_id' => $analisis->id,
                'message'     => 'Analisis berhasil disimpan',
            ]);

        } catch (\Exception $e) {
            \Log::error('Callback error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

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

        if (request()->wantsJson()) {
            return response()->json(['analisis' => $analisis]);
        }

        return view('analisis-hasil', compact('folder', 'analisis'));
    }
}