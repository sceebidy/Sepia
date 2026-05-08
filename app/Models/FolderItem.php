<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderItem extends Model
{
    protected $table = 'folder_items';

    protected $fillable = [
        'folder_id',
        'tipe',
        'judul',
        'konten',
        'file_path',
        'file_nama',
        'file_tipe',
        'file_ukuran',
        'processed',
        'hasil_rangkuman',
        'ditambahkan_oleh',
    ];

    protected $casts = [
        'processed' => 'boolean',
    ];

    // ── Relasi ke folder
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    // ── Format ukuran file
    public function ukuranFormatted()
    {
        if (!$this->file_ukuran) return '-';
        $kb = $this->file_ukuran / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }

    // ── Icon per tipe
    public function tipeIcon()
    {
        return match($this->tipe) {
            'file'    => '📄',
            'link'    => '🔗',
            'catatan' => '📝',
            default   => '📎',
        };
    }
}