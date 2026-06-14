<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskAssessment extends Model
{
    protected $table = 'risk_assessment';
    protected $fillable = ['analisis_id', 'label', 'nilai', 'warna', 'keterangan', 'urutan'];

    public function analisis()
    {
        return $this->belongsTo(AnalisisKasus::class, 'analisis_id');
    }
}