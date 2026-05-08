<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\FolderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    // ── Halaman daftar semua folder (datapool)
    public function index()
    {
        $folders = Folder::withCount('items')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalItem = FolderItem::count();

        return view('datapool', compact('folders', 'totalItem'));
    }

    // ── Buat folder baru
    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'emoji'       => 'nullable|string|max:10',
            'warna_stripe'=> 'nullable|string|max:20',
            'status'      => 'required|in:aktif,penyidikan,penuntutan,inkracht,baru',
        ]);

        Folder::create([
            'nama'         => $request->nama,
            'deskripsi'    => $request->deskripsi,
            'emoji'        => $request->emoji ?? '📁',
            'warna_stripe' => $request->warna_stripe ?? '#1a5c2e',
            'status'       => $request->status,
            'dibuat_oleh'  => 'C. Rasyid', // nanti ganti dengan auth()->user()->name
        ]);

        return redirect()->route('datapool.index')->with('success', 'Folder berhasil dibuat.');
    }

    // ── Halaman detail folder + daftar items
    public function show(Folder $folder)
    {
        $items = $folder->items()->orderBy('created_at', 'desc')->get();
        return view('folder-detail', compact('folder', 'items'));
    }

    // ── Tambah item ke folder (file / link / catatan)
    public function addItem(Request $request, Folder $folder)
    {
        $request->validate([
            'tipe'  => 'required|in:file,link,catatan',
            'judul' => 'required|string|max:255',
        ]);

        $data = [
            'folder_id'        => $folder->id,
            'tipe'             => $request->tipe,
            'judul'            => $request->judul,
            'ditambahkan_oleh' => 'C. Rasyid', // nanti ganti dengan auth()->user()->name
        ];

        // ── Tipe: FILE
        if ($request->tipe === 'file' && $request->hasFile('file')) {
            $request->validate([
                'file' => 'required|file|max:20480', // max 20MB
            ]);
            $file      = $request->file('file');
            $path      = $file->store("folders/{$folder->id}", 'public');
            $data['file_path']  = $path;
            $data['file_nama']  = $file->getClientOriginalName();
            $data['file_tipe']  = $file->getMimeType();
            $data['file_ukuran']= $file->getSize();
        }

        // ── Tipe: LINK
        if ($request->tipe === 'link') {
            $request->validate(['konten' => 'required|url']);
            $data['konten'] = $request->konten;
        }

        // ── Tipe: CATATAN
        if ($request->tipe === 'catatan') {
            $request->validate(['konten' => 'required|string']);
            $data['konten'] = $request->konten;
        }

        FolderItem::create($data);

        return redirect()->route('datapool.show', $folder)->with('success', 'Item berhasil ditambahkan.');
    }

    // ── Hapus item
    public function deleteItem(FolderItem $item)
    {
        $folder = $item->folder;

        // hapus file fisik jika ada
        if ($item->tipe === 'file' && $item->file_path) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return redirect()->route('datapool.show', $folder)->with('success', 'Item berhasil dihapus.');
    }

    // ── Hapus folder beserta semua itemnya
    public function destroy(Folder $folder)
    {
        // hapus semua file fisik
        foreach ($folder->items->where('tipe', 'file') as $item) {
            if ($item->file_path) {
                Storage::disk('public')->delete($item->file_path);
            }
        }

        $folder->delete(); // cascadeOnDelete akan hapus folder_items otomatis

        return redirect()->route('datapool.index')->with('success', 'Folder berhasil dihapus.');
    }
}