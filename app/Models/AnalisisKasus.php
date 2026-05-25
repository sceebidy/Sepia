<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AnalisisKasus extends Model
{
    protected $table = 'analisis_kasus';
    protected $fillable = [
        'folder_id', 'judul', 'perihal', 'periode', 'wilayah',
        'tanggal_analisis', 'tingkat_risiko', 'prediksi_vonis',
        'jumlah_sumber', 'model_versi',
        'ringkasan_eksekutif', 'deskripsi', 'interpretasi',
        'ringkasan_intelijen', 'konteks_geopolitik', 'catatan_analis',
        'klasifikasi_dokumen', 'eei', 'coa', 'early_warning',
        'matrix_analisis', 'jabatan_rekomendasi', 'fakta_fakta',
        'matrix_analisis', 'jabatan_rekomendasi', 'fakta_fakta', 'analisis_intelijen',
    ];

    protected $casts = [
        'eei'                  => 'array',
        'coa'                  => 'array',
        'early_warning'        => 'array',
        'matrix_analisis'      => 'array',
    ];

    public function folder()      { return $this->belongsTo(Folder::class, 'folder_id'); }
    public function swotItems()   { return $this->hasMany(SwotItem::class, 'analisis_id')->orderBy('urutan'); }
    public function aktor()       { return $this->hasMany(AktorKasus::class, 'analisis_id'); }
    public function timeline()    { return $this->hasMany(TimelineKasus::class, 'analisis_id')->orderBy('urutan'); }
    public function rekomendasi() { return $this->hasMany(RekomendasiKasus::class, 'analisis_id')->orderBy('urutan'); }
    public function confidence()  { return $this->hasOne(ConfidenceKasus::class, 'analisis_id'); }
    public function riskItems()   { return $this->hasMany(RiskAssessment::class, 'analisis_id')->orderBy('urutan'); }

    public function avgConfidence()
    {
        $c = $this->confidence;
        if (!$c) return 0;
        return round(($c->kelengkapan_data + $c->konsistensi_sumber + $c->kualitas_dokumen + $c->kedalaman_analisis) / 4);
    }
}