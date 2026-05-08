<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = 'issues';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'sub_kategori',
        'risiko',
        'status',
        'wilayah',
        'sumber',
    ];

    // ── Scope: filter kategori
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // ── Scope: filter risiko
    public function scopeRisiko($query, $risiko)
    {
        return $query->where('risiko', $risiko);
    }

    // ── Scope: hanya yang aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // ── Relasi ke analisis
    public function analisis()
    {
        return $this->hasMany(Analisis::class, 'issue_id');
    }
}