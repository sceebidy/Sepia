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
        // ── Tambahan untuk integrasi AI ──
        'ai_usage_id',
        'dari_ai',
    ];

    protected $casts = [
        'dari_ai' => 'boolean',
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

    // ── Scope: hanya dari AI
    public function scopeDariAi($query)
    {
        return $query->where('dari_ai', true);
    }

    // ── Relasi ke analisis
    public function analisis()
    {
        return $this->hasMany(Analisis::class, 'issue_id');
    }

    // ── Relasi ke ai_usage (opsional, nullable)
    public function aiUsage()
    {
        return $this->belongsTo(AiUsage::class, 'ai_usage_id');
    }
}