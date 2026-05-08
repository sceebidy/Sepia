<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tokoh extends Model
{
    protected $table = 'tokoh';

    protected $fillable = [
        'nama',
        'inisial',
        'kategori',
        'peran',
        'wilayah',
        'risiko',
        'afiliasi',
        'catatan',
        'status',
    ];

    // ── Scope: hanya yang aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // ── Scope: filter risiko
    public function scopeRisiko($query, $risiko)
    {
        return $query->where('risiko', $risiko);
    }
}