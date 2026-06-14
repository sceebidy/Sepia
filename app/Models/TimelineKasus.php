<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineKasus extends Model
{
    protected $table = 'timeline_kasus';
    protected $fillable = ['analisis_id', 'tanggal', 'keterangan', 'warna_dot', 'urutan'];

    public function analisis()
    {
        return $this->belongsTo(AnalisisKasus::class, 'analisis_id');
    }
}