<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktorKasus extends Model
{
    protected $table = 'aktor_kasus';

    protected $fillable = [
        'analisis_id',
        'nama',
        'inisial',
        'peran',
        'status',
        'warna_avatar',
    ];

    public function analisis()
    {
        return $this->belongsTo(AnalisisKasus::class, 'analisis_id');
    }
}