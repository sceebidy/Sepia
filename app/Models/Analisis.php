<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analisis extends Model
{
    protected $table = 'analisis';

    protected $fillable = [
        'judul',
        'konten',
        'kategori',
        'issue_id',
        'status',
        'analis',
    ];

    // ── Relasi ke issue
    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }
}   