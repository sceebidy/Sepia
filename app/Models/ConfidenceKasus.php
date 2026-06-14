<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfidenceKasus extends Model
{
    protected $table = 'confidence_kasus';
    protected $fillable = ['analisis_id', 'kelengkapan_data', 'konsistensi_sumber', 'kualitas_dokumen', 'kedalaman_analisis'];

    public function analisis()
    {
        return $this->belongsTo(AnalisisKasus::class, 'analisis_id');
    }
}