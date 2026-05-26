<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSearchController extends Controller
{
    /**
     * GET /datapool/{folder}/search
     * Cari berita menggunakan OpenAI Web Search
     */
    public function search(Request $request, Folder $folder)
    {
        $query   = $request->input('q', $folder->nama);
        $results = [];
        $error   = null;

        if ($query) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type'  => 'application/json',
                ])->timeout(30)->post('https://api.openai.com/v1/responses', [
                    'model' => 'gpt-4o',
                    'tools' => [['type' => 'web_search_preview']],
                    'input' => 'Cari 10 berita atau artikel terbaru tentang: "' . $query . '" yang terjadi di Kota Medan, Sumatera Utara. ' .
                'Fokuskan pencarian pada berita lokal Medan dan Sumatera Utara. ' .
                'Kembalikan HANYA JSON array tanpa markdown, format: ' .
                '[{"judul":"...","url":"...","snippet":"...","sumber":"..."}]. ' .
                'Maksimal 10 hasil, prioritaskan sumber berita lokal Medan dan Sumatera Utara.',
                        ]);

                if ($response->successful()) {
                    $data    = $response->json();
                    $content = '';

                    // Ambil teks dari output
                    foreach ($data['output'] ?? [] as $out) {
                        if ($out['type'] === 'message') {
                            foreach ($out['content'] ?? [] as $c) {
                                if ($c['type'] === 'output_text') {
                                    $content = $c['text'] ?? '';
                                }
                            }
                        }
                    }

                    // Parse JSON dari response
                    $content = trim(preg_replace('/```json|```/', '', $content));
                    preg_match('/\[.*\]/s', $content, $matches);
                    $parsed = json_decode($matches[0] ?? $content, true);

                    if (is_array($parsed)) {
                        $results = array_slice($parsed, 0, 10);
                    } else {
                        $error = 'Gagal parse hasil pencarian.';
                        Log::warning('[SEPIA Search] Parse gagal: ' . substr($content, 0, 200));
                    }
                } else {
                    $error = 'OpenAI error: ' . $response->status() . ' — ' . $response->body();
                    Log::error('[SEPIA Search] ' . $error);
                }
            } catch (\Exception $e) {
                $error = 'Gagal menghubungi OpenAI: ' . $e->getMessage();
                Log::error('[SEPIA Search] Exception: ' . $e->getMessage());
            }
        }

        // Return JSON jika request AJAX (dari modal)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['results' => $results, 'error' => $error]);
        }

        return view('google-search', compact('folder', 'query', 'results', 'error'));
    }

    /**
     * POST /datapool/{folder}/search/simpan
     * Simpan link terpilih ke folder_items
     */
   public function simpan(Request $request, Folder $folder)
{
    $links = $request->input('links', []);
    $saved = 0;

    foreach ($links as $link) {
        $judul = $link['judul'] ?? 'Sumber dari pencarian';
        $url   = $link['url']   ?? null;
        if (!$url) continue;

        $exists = $folder->items()->where('tipe', 'link')->where('konten', $url)->exists();
        if (!$exists) {
            $folder->items()->create([
                'tipe'             => 'link',
                'judul'            => $judul,
                'konten'           => $url,
                'ditambahkan_oleh' => 'Analis SEPIA',
            ]);
            $saved++;
        }
    }

    // PAKSA SELALU MENGEMBALIKAN JSON (Hapus pengecekan if sebelumnya)
    return response()->json([
        'success' => true, 
        'saved'   => $saved, 
        'message' => "{$saved} link ditambahkan"
    ]);
}
}