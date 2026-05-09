<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AiUsage;
use App\Models\FolderItem;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class N8nController extends Controller
{
    // ──────────────────────────────────────────────────────────
    //  POST /api/n8n/process/{item}
    //  Dipanggil dari tombol "Proses dengan AI" di folder-detail
    // ──────────────────────────────────────────────────────────
    public function process(FolderItem $item)
    {
        // Jangan proses ulang kalau sedang dalam antrian
        $sedangProses = AiUsage::where('folder_item_id', $item->id)
            ->whereIn('status', ['pending', 'proses'])
            ->exists();

        if ($sedangProses) {
            return response()->json([
                'success' => false,
                'message' => 'Item ini sedang dalam proses AI. Harap tunggu.',
            ], 422);
        }

        // Buat record ai_usage dengan status pending
        $aiUsage = AiUsage::create([
            'folder_item_id' => $item->id,
            'status'         => 'pending',
            'model'          => 'gpt-4o',
            'dipicu_oleh'    => 'C. Rasyid',
        ]);

        // Siapkan payload untuk n8n
        $payload = [
            'ai_usage_id'   => $aiUsage->id,
            'item_id'       => $item->id,
            'folder_id'     => $item->folder_id,
            'tipe'          => $item->tipe,
            'judul'         => $item->judul,
            'konten'        => $item->konten,
            'callback_url'  => url('/api/n8n/callback'),
        ];

        // Sertakan URL file jika tipe file
        if ($item->tipe === 'file' && $item->file_path) {
            $payload['file_url']  = Storage::disk('public')->url($item->file_path);
            $payload['file_nama'] = $item->file_nama;
            $payload['file_tipe'] = $item->file_tipe;
        }

        // Kirim ke n8n Webhook
        $n8nWebhookUrl = config('services.n8n.webhook_url');

        try {
            $response = Http::timeout(10)
                ->post($n8nWebhookUrl, $payload);

            if ($response->successful()) {
                // Update status → proses
                $aiUsage->update(['status' => 'proses']);

                return response()->json([
                    'success'     => true,
                    'message'     => 'Permintaan AI berhasil dikirim. Proses sedang berjalan.',
                    'ai_usage_id' => $aiUsage->id,
                ]);
            }

            // n8n merespons tapi error
            $aiUsage->update([
                'status'        => 'gagal',
                'error_message' => 'n8n merespons dengan status: ' . $response->status(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ke n8n. Cek konfigurasi webhook.',
            ], 500);

        } catch (\Exception $e) {
            // n8n tidak bisa dijangkau (belum jalan, salah URL, dll)
            $aiUsage->update([
                'status'        => 'gagal',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('N8n webhook error', [
                'item_id'      => $item->id,
                'ai_usage_id'  => $aiUsage->id,
                'error'        => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'n8n tidak dapat dijangkau: ' . $e->getMessage(),
            ], 503);
        }
    }

    // ──────────────────────────────────────────────────────────
    //  POST /api/n8n/callback
    //  Dipanggil oleh n8n setelah AI selesai memproses
    //
    //  Expected payload dari n8n:
    //  {
    //    "ai_usage_id"     : 1,
    //    "status"          : "selesai" | "gagal",
    //    "secret"          : "xxx",           ← validasi sederhana
    //    "judul_isu"       : "...",
    //    "rangkuman"       : "...",
    //    "kategori"        : "politik",
    //    "sub_kategori"    : "elektoral",     ← opsional
    //    "risiko"          : "tinggi",        ← opsional
    //    "wilayah"         : "Sumatra Utara", ← opsional
    //    "model"           : "gpt-4o",
    //    "prompt_tokens"   : 1200,
    //    "completion_tokens": 400,
    //    "durasi_detik"    : 8,
    //    "error_message"   : null
    //  }
    // ──────────────────────────────────────────────────────────
    public function callback(Request $request)
    {
        // ── Validasi secret key sederhana ──
        $secret = config('services.n8n.callback_secret');
        if ($secret && $request->input('secret') !== $secret) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ── Validasi payload ──
        $validated = $request->validate([
            'ai_usage_id'        => 'required|exists:ai_usage,id',
            'status'             => 'required|in:selesai,gagal',
            'judul_isu'          => 'nullable|string|max:255',
            'rangkuman'          => 'nullable|string',
            'kategori'           => 'nullable|in:ideologi,politik,ekonomi,sosbud,hankam',
            'sub_kategori'       => 'nullable|string|max:100',
            'risiko'             => 'nullable|in:tinggi,sedang,rendah',
            'wilayah'            => 'nullable|string|max:255',
            'model'              => 'nullable|string|max:50',
            'prompt_tokens'      => 'nullable|integer',
            'completion_tokens'  => 'nullable|integer',
            'durasi_detik'       => 'nullable|integer',
            'error_message'      => 'nullable|string',
        ]);

        $aiUsage = AiUsage::find($validated['ai_usage_id']);
        $item    = $aiUsage->folderItem;

        if ($validated['status'] === 'gagal') {
            // ── Proses gagal ──
            $aiUsage->update([
                'status'        => 'gagal',
                'error_message' => $validated['error_message'] ?? 'Gagal tanpa pesan error.',
                'model'         => $validated['model'] ?? $aiUsage->model,
                'durasi_detik'  => $validated['durasi_detik'] ?? null,
            ]);

            Log::warning('N8n AI processing failed', [
                'ai_usage_id' => $aiUsage->id,
                'item_id'     => $item->id,
                'error'       => $validated['error_message'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Status gagal dicatat.',
            ]);
        }

        // ── Proses berhasil ──
        $totalTokens = ($validated['prompt_tokens'] ?? 0)
                     + ($validated['completion_tokens'] ?? 0);

        $aiUsage->update([
            'status'             => 'selesai',
            'model'              => $validated['model'] ?? $aiUsage->model,
            'prompt_tokens'      => $validated['prompt_tokens'] ?? null,
            'completion_tokens'  => $validated['completion_tokens'] ?? null,
            'total_tokens'       => $totalTokens ?: null,
            'durasi_detik'       => $validated['durasi_detik'] ?? null,
            'hasil_raw'          => json_encode($validated),
            'error_message'      => null,
        ]);

        // ── Update folder_item: tandai sudah diproses ──
        $item->update([
            'processed'       => true,
            'hasil_rangkuman' => $validated['rangkuman'] ?? null,
        ]);

        // ── Buat issue baru dari hasil AI (jika ada judul & kategori) ──
        $issueId = null;
        if (!empty($validated['judul_isu']) && !empty($validated['kategori'])) {
            $issue = Issue::create([
                'ai_usage_id'  => $aiUsage->id,
                'dari_ai'      => true,
                'judul'        => $validated['judul_isu'],
                'deskripsi'    => $validated['rangkuman'] ?? null,
                'kategori'     => $validated['kategori'],
                'sub_kategori' => $validated['sub_kategori'] ?? null,
                'risiko'       => $validated['risiko'] ?? 'sedang',
                'wilayah'      => $validated['wilayah'] ?? null,
                'status'       => 'aktif',
                'sumber'       => 'AI — ' . ($item->judul ?? 'folder item'),
            ]);
            $issueId = $issue->id;
        }

        Log::info('N8n callback processed successfully', [
            'ai_usage_id' => $aiUsage->id,
            'item_id'     => $item->id,
            'issue_id'    => $issueId,
            'tokens'      => $totalTokens,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Hasil AI berhasil disimpan.',
            'ai_usage_id' => $aiUsage->id,
            'issue_id'    => $issueId,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    //  POST /api/n8n/test-callback
    //  Simulasi callback dari n8n — untuk testing via Postman
    //  TANPA perlu n8n beneran jalan
    // ──────────────────────────────────────────────────────────
    public function testCallback(Request $request)
    {
        // Hanya aktif di environment non-production
        if (app()->isProduction()) {
            return response()->json(['message' => 'Not available in production'], 403);
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:folder_items,id',
        ]);

        $item = FolderItem::find($validated['item_id']);

        // Buat ai_usage dummy
        $aiUsage = AiUsage::create([
            'folder_item_id'  => $item->id,
            'status'          => 'selesai',
            'model'           => 'gpt-4o (simulasi)',
            'prompt_tokens'   => rand(800, 2000),
            'completion_tokens' => rand(200, 600),
            'total_tokens'    => rand(1000, 2600),
            'durasi_detik'    => rand(3, 12),
            'dipicu_oleh'     => 'C. Rasyid',
        ]);
        $aiUsage->update(['total_tokens' => $aiUsage->prompt_tokens + $aiUsage->completion_tokens]);

        // Simulasi hasil AI
        $kategoriList    = ['ideologi', 'politik', 'ekonomi', 'sosbud', 'hankam'];
        $subKategoriMap  = [
            'ideologi' => 'radikalisme',
            'politik'  => 'elektoral',
            'ekonomi'  => 'korupsi',
            'sosbud'   => 'hoaks_sara',
            'hankam'   => 'siber',
        ];
        $risikoList = ['tinggi', 'sedang', 'rendah'];
        $kategori   = $kategoriList[array_rand($kategoriList)];

        $rangkuman = "Dokumen ini membahas " . strtolower($item->judul) . ". "
            . "Berdasarkan analisis AI, terdapat indikasi permasalahan di sektor $kategori "
            . "yang perlu mendapat perhatian lebih lanjut dari tim analis.";

        // Update item
        $item->update([
            'processed'       => true,
            'hasil_rangkuman' => $rangkuman,
        ]);

        // Insert issue simulasi
        $issue = Issue::create([
            'ai_usage_id'  => $aiUsage->id,
            'dari_ai'      => true,
            'judul'        => '[SIMULASI] ' . $item->judul,
            'deskripsi'    => $rangkuman,
            'kategori'     => $kategori,
            'sub_kategori' => $subKategoriMap[$kategori],
            'risiko'       => $risikoList[array_rand($risikoList)],
            'wilayah'      => 'Sumatra Utara',
            'status'       => 'aktif',
            'sumber'       => 'AI Simulasi',
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Simulasi callback berhasil. Cek folder-detail untuk melihat hasilnya.',
            'ai_usage_id' => $aiUsage->id,
            'issue_id'    => $issue->id,
            'preview'     => [
                'rangkuman' => $rangkuman,
                'kategori'  => $kategori,
                'tokens'    => $aiUsage->total_tokens,
            ],
        ]);
    }
}