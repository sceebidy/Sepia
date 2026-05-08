<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $table = 'folders';

    protected $fillable = [
        'nama',
        'deskripsi',
        'emoji',
        'warna_stripe',
        'status',
        'dibuat_oleh',
    ];

    // ── Relasi ke items
    public function items()
    {
        return $this->hasMany(FolderItem::class, 'folder_id');
    }

    // ── Hitung jumlah item
    public function jumlahItem()
    {
        return $this->items()->count();
    }

    // ── Warna badge status
    public function statusColor()
    {
        return match($this->status) {
            'aktif'       => ['bg' => '#f0f7f2', 'text' => '#1a5c2e', 'border' => '#b6d9c3'],
            'penyidikan'  => ['bg' => '#fffbeb', 'text' => '#92400e', 'border' => '#fde68a'],
            'penuntutan'  => ['bg' => '#fce7f3', 'text' => '#9d174d', 'border' => '#f9a8d4'],
            'inkracht'    => ['bg' => '#eff6ff', 'text' => '#1e40af', 'border' => '#bfdbfe'],
            'baru'        => ['bg' => '#f3f4f6', 'text' => '#6b7280', 'border' => '#e5e7eb'],
            default       => ['bg' => '#f3f4f6', 'text' => '#6b7280', 'border' => '#e5e7eb'],
        };
    }
}