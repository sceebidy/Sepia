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

    /**
     * FIX: Relasi Jembatan ke Folder melalui AnalisisKasus
     * Menghubungkan Issue -> AnalisisKasus (RPI) -> Folder
     */
    public function folder()
    {
        return $this->hasOneThrough(
            Folder::class,        // Model tujuan akhir
            AnalisisKasus::class, // Model jembatan perantara
            'id',                 // Foreign key di analisis_kasus (analisis_kasus.id)
            'id',                 // Foreign key di folders (folders.id)
            'id',                 // Local key di issues (issues.id) - dicocokkan via jembatan relasi internal
            'folder_id'           // Local key di analisis_kasus (analisis_kasus.folder_id)
        );
    }
}