<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\FolderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    // ──────────────────────────────────────────────
    //  GET /datapool
    // ──────────────────────────────────────────────
    public function index()
    {
        $folders = Folder::withCount('items')
            ->orderByRaw("FIELD(status, 'aktif', 'penyidikan', 'penuntutan', 'baru', 'inkracht')")
            ->orderBy('created_at', 'desc')
            ->get();

        $totalItem = FolderItem::count();

        return view('datapool', compact('folders', 'totalItem'));
    }

    // ──────────────────────────────────────────────
    //  POST /datapool
    // ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'         => 'required|string|max:255',
            'deskripsi'    => 'nullable|string|max:1000',
            'emoji'        => 'nullable|string|max:10',
            'warna_stripe' => 'nullable|string|max:20',
            'status'       => 'nullable|in:baru,aktif,penyidikan,penuntutan,inkracht',
        ], [
            'nama.required' => 'Nama folder tidak boleh kosong.',
            'nama.max'      => 'Nama folder maksimal 255 karakter.',
        ]);

        Folder::create([
            'nama'         => $validated['nama'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'emoji'        => $validated['emoji'] ?? '📁',
            'warna_stripe' => $validated['warna_stripe'] ?? '#1a5c2e',
            'status'       => $validated['status'] ?? 'baru',
            'dibuat_oleh'  => 'C. Rasyid',
        ]);

        return redirect()->route('datapool.index')
            ->with('success', 'Folder "' . $validated['nama'] . '" berhasil dibuat.');
    }

    // ──────────────────────────────────────────────
    //  GET /datapool/{folder}
    // ──────────────────────────────────────────────
    public function show(Folder $folder)
{
    $items = $folder->items()->orderBy('created_at', 'desc')->get();
    
    // tambah ini
    $analisis = \App\Models\AnalisisKasus::where('folder_id', $folder->id)->first();
    
    return view('folder-detail', compact('folder', 'items', 'analisis'));
}

    // ──────────────────────────────────────────────
    //  PUT /datapool/{folder}
    // ──────────────────────────────────────────────
    public function update(Request $request, Folder $folder)
    {
        $validated = $request->validate([
            'nama'         => 'required|string|max:255',
            'deskripsi'    => 'nullable|string|max:1000',
            'emoji'        => 'nullable|string|max:10',
            'warna_stripe' => 'nullable|string|max:20',
            'status'       => 'nullable|in:baru,aktif,penyidikan,penuntutan,inkracht',
        ], [
            'nama.required' => 'Nama folder tidak boleh kosong.',
        ]);

        $folder->update([
            'nama'         => $validated['nama'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'emoji'        => $validated['emoji'] ?? $folder->emoji,
            'warna_stripe' => $validated['warna_stripe'] ?? $folder->warna_stripe,
            'status'       => $validated['status'] ?? $folder->status,
        ]);

        return redirect()->route('datapool.index')
            ->with('success', 'Folder "' . $folder->nama . '" berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────
    //  DELETE /datapool/{folder}
    // ──────────────────────────────────────────────
    public function destroy(Folder $folder)
    {
        $namaFolder = $folder->nama;

        // Hapus file fisik tiap item
        foreach ($folder->items as $item) {
            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }
        }

        $folder->items()->delete();
        $folder->delete();

        return redirect()->route('datapool.index')
            ->with('success', 'Folder "' . $namaFolder . '" dan semua isinya berhasil dihapus.');
    }

    // ──────────────────────────────────────────────
    //  POST /datapool/{folder}/items
    // ──────────────────────────────────────────────
    public function addItem(Request $request, Folder $folder)
    {
        $tipe = $request->input('tipe');

        $rules = [
            'tipe'  => 'required|in:file,link,catatan',
            'judul' => 'required|string|max:255',
        ];

        if ($tipe === 'file') {
            $rules['file'] = 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,txt,csv,ppt,pptx';
        } elseif ($tipe === 'link') {
            $rules['konten_link'] = 'required|url|max:2048';
        } elseif ($tipe === 'catatan') {
            $rules['konten_catatan'] = 'required|string|max:5000';
        }

        $messages = [
            'judul.required'          => 'Judul item tidak boleh kosong.',
            'file.required'           => 'File wajib diupload untuk tipe File.',
            'file.max'                => 'Ukuran file maksimal 20MB.',
            'file.mimes'              => 'Format file tidak didukung.',
            'konten_link.required'    => 'URL tidak boleh kosong.',
            'konten_link.url'         => 'Format URL tidak valid. Pastikan diawali https://',
            'konten_catatan.required' => 'Isi catatan tidak boleh kosong.',
        ];

        $validated = $request->validate($rules, $messages);

        $konten = match($tipe) {
            'link'    => $validated['konten_link'],
            'catatan' => $validated['konten_catatan'],
            default   => null,
        };

        $data = [
            'folder_id'        => $folder->id,
            'tipe'             => $tipe,
            'judul'            => $validated['judul'],
            'konten'           => $konten,
            'ditambahkan_oleh' => 'C. Rasyid',
        ];

        if ($tipe === 'file' && $request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $path = $file->store('folder-items', 'public');

            $data['file_path']   = $path;
            $data['file_nama']   = $file->getClientOriginalName();
            $data['file_tipe']   = $file->getClientMimeType();
            $data['file_ukuran'] = $file->getSize();
        }

        FolderItem::create($data);

        return redirect()->route('datapool.show', $folder)
            ->with('success', 'Sumber "' . $validated['judul'] . '" berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────
    //  PATCH /datapool/items/{item}
    // ──────────────────────────────────────────────
    public function updateItem(Request $request, FolderItem $item)
    {
        $rules = ['judul' => 'required|string|max:255'];

        if (in_array($item->tipe, ['link', 'catatan'])) {
            $rules['konten'] = $item->tipe === 'link'
                ? 'required|url|max:2048'
                : 'required|string|max:5000';
        }

        $messages = [
            'judul.required'  => 'Judul tidak boleh kosong.',
            'konten.required' => 'Konten tidak boleh kosong.',
            'konten.url'      => 'Format URL tidak valid.',
        ];

        $validated = $request->validate($rules, $messages);

        $updateData = ['judul' => $validated['judul']];
        if (isset($validated['konten'])) {
            $updateData['konten'] = $validated['konten'];
        }

        $item->update($updateData);

        return redirect()->route('datapool.show', $item->folder)
            ->with('success', 'Item "' . $item->judul . '" berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────
    //  DELETE /datapool/items/{item}
    // ──────────────────────────────────────────────
    public function deleteItem(FolderItem $item)
    {
        $folder   = $item->folder;
        $namaItem = $item->judul;

        if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return redirect()->route('datapool.show', $folder)
            ->with('success', 'Item "' . $namaItem . '" berhasil dihapus.');
    }
}