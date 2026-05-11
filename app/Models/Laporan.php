<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $fillable = [
        'folder_id',
        'analisis_id',
        'judul',
        'nomor_laporan',
        'tingkat_risiko',
        'prediksi_vonis',
        'jumlah_sumber',
        'jumlah_aktor',
        'jumlah_rekomendasi',
        'dibuat_oleh',
        'file_path',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function analisis()
    {
        return $this->belongsTo(AnalisisKasus::class, 'analisis_id');
    }

    public function nomorFormatted()
    {
        return 'LAP-' . str_pad($this->id, 4, '0', STR_PAD_LEFT) . '/' . date('Y', strtotime($this->created_at));
    }

    public function risikoLabel()
    {
        if ($this->tingkat_risiko >= 7) return 'tinggi';
        if ($this->tingkat_risiko >= 4) return 'sedang';
        return 'rendah';
    }
}