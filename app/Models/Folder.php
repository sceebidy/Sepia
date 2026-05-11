<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'folders';

    protected $fillable = [
        'nama',
        'deskripsi',
        'emoji',
        'warna_stripe',
        'status',
        'dibuat_oleh',
    ];

    // ── Relasi ──────────────────────────────
    public function items()
    {
        return $this->hasMany(FolderItem::class);
    }

    public function analisis()
{
    return $this->hasMany(\App\Models\AnalisisKasus::class, 'folder_id');
}
    // ── Helper: warna badge per status ──────
    public function statusColor(): array
    {
        return match ($this->status) {
            'aktif'      => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
            'penyidikan' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a'],
            'penuntutan' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
            'inkracht'   => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'border' => '#d1d5db'],
            default      => ['bg' => '#f0fdf4', 'text' => '#0f766e', 'border' => '#99f6e4'], // baru
        };
    }
}