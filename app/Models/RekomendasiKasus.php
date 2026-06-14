<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekomendasiKasus extends Model
{
    protected $table = 'rekomendasi_kasus';
    protected $fillable = ['analisis_id', 'judul', 'deskripsi', 'prioritas', 'urutan'];

    public function analisis()
    {
        return $this->belongsTo(AnalisisKasus::class, 'analisis_id');
    }
}