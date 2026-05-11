<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\AnalisisKasus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DistribusiController extends Controller
{
    public function show(Folder $folder, AnalisisKasus $analisis)
    {
        $analisis->load([
            'swotItems',
            'aktor',
            'timeline',
            'rekomendasi',
            'riskItems',
            'confidence',
        ]);

        return view('distribusi', compact('folder', 'analisis'));
    }

    public function generate(Request $request, Folder $folder, AnalisisKasus $analisis)
    {
        $analisis->load(['swotItems', 'aktor', 'timeline', 'rekomendasi', 'riskItems']);

        $swotS = $analisis->swotItems->where('tipe', 'S')->pluck('isi')->join(', ');
        $swotW = $analisis->swotItems->where('tipe', 'W')->pluck('isi')->join(', ');
        $swotO = $analisis->swotItems->where('tipe', 'O')->pluck('isi')->join(', ');
        $swotT = $analisis->swotItems->where('tipe', 'T')->pluck('isi')->join(', ');
        $aktor = $analisis->aktor->map(fn($a) => "{$a->nama} ({$a->peran}, {$a->status})")->join(', ');
        $timeline = $analisis->timeline->map(fn($t) => "{$t->tanggal}: {$t->keterangan}")->join(' | ');
        $rekomendasi = $analisis->rekomendasi->map(fn($r) => "{$r->judul} [{$r->prioritas}]")->join(', ');

        $ringkasan = "Kasus: {$analisis->judul}\nTingkat Risiko: {$analisis->tingkat_risiko}/10\nPrediksi Vonis: {$analisis->prediksi_vonis}\n\nSWOT:\n- Kekuatan: {$swotS}\n- Kelemahan: {$swotW}\n- Peluang: {$swotO}\n- Ancaman: {$swotT}\n\nAktor: {$aktor}\nTimeline: {$timeline}\nRekomendasi: {$rekomendasi}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'Kamu adalah analis hukum senior Indonesia. Tentukan instansi yang perlu dihubungi dan buat email resmi dalam Bahasa Indonesia. Jawab HANYA dengan JSON array valid tanpa markdown dan tanpa teks tambahan apapun.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => "Buat daftar email distribusi untuk kasus berikut.\n\n{$ringkasan}\n\nReturn HANYA JSON array tanpa teks lain:\n[{\"instansi\":\"Nama Instansi\",\"tipe\":\"polres\",\"to\":\"email@domain.go.id\",\"cc\":\"\",\"subject\":\"[LAPORAN SEPIA] Judul\",\"body\":\"Isi surat lengkap formal...\"}]\n\nATURAN:\n- Field 'to' HARUS berupa alamat email valid format user@domain.go.id\n- Jangan tambahkan teks apapun sebelum atau sesudah JSON array\n- Minimal 3 instansi yang relevan dengan lokasi dan jenis kasus\n- Isi surat harus formal dan lengkap dalam Bahasa Indonesia",
                    ],
                ],
                'temperature' => 0.3,
                'max_tokens'  => 3000,
            ]);

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/```json|```/', '', $content);
            $content = trim($content);

            preg_match('/\[.*\]/s', $content, $matches);
            if (!empty($matches[0])) {
                $content = $matches[0];
            }

            $emails = json_decode($content, true);

            if (!is_array($emails)) {
                return response()->json(['error' => 'Format response AI tidak valid', 'raw' => $content], 500);
            }

            return response()->json([
                'success' => true,
                'emails'  => $emails,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}