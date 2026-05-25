<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\FolderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleSearchController extends Controller
{
    /**
     * GET /datapool/{folder}/search
     * Tampilkan hasil pencarian Google
     */
    public function search(Request $request, Folder $folder)
    {
        $query = $request->input('q', $folder->nama);
        $results = [];
        $error = null;

        if ($query) {
            try {
                $response = Http::timeout(10)->get('https://www.googleapis.com/customsearch/v1', [
                    'key'  => env('GOOGLE_SEARCH_API_KEY'),
                    'cx'   => env('GOOGLE_SEARCH_ENGINE_ID'),
                    'q'    => $query,
                    'num'  => 10,
                    'lr'   => 'lang_id',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $results = $data['items'] ?? [];
                } else {
                    $error = 'Google API error: ' . $response->status();
                }
            } catch (\Exception $e) {
                $error = 'Gagal menghubungi Google: ' . $e->getMessage();
            }
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
            $judul = $link['judul'] ?? 'Sumber dari Google';
            $url   = $link['url']   ?? null;

            if (!$url) continue;

            // Cek duplikat
            $exists = $folder->items()
                ->where('tipe', 'link')
                ->where('konten', $url)
                ->exists();

            if (!$exists) {
                $folder->items()->create([
                    'tipe'              => 'link',
                    'judul'             => $judul,
                    'konten'            => $url,
                    'ditambahkan_oleh'  => 'C. Rasyid',
                ]);
                $saved++;
            }
        }

        return redirect()->route('datapool.show', $folder)
            ->with('success', "{$saved} link berhasil ditambahkan ke folder.");
    }
}