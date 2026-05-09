<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

// ── app/Models/SwotItem.php
class SwotItem extends Model {
    protected $table = 'swot_items';
    protected $fillable = ['analisis_id', 'tipe', 'isi', 'urutan'];
    public function analisis() { return $this->belongsTo(AnalisisKasus::class, 'analisis_id'); }
}

// ── app/Models/AktorKasus.php
class AktorKasus extends Model {
    protected $table = 'aktor_kasus';
    protected $fillable = ['analisis_id', 'nama', 'inisial', 'peran', 'status', 'warna_avatar'];
    public function analisis() { return $this->belongsTo(AnalisisKasus::class, 'analisis_id'); }
}

// ── app/Models/TimelineKasus.php
class TimelineKasus extends Model {
    protected $table = 'timeline_kasus';
    protected $fillable = ['analisis_id', 'tanggal', 'keterangan', 'warna_dot', 'urutan'];
    public function analisis() { return $this->belongsTo(AnalisisKasus::class, 'analisis_id'); }
}

// ── app/Models/RekomendasiKasus.php
class RekomendasiKasus extends Model {
    protected $table = 'rekomendasi_kasus';
    protected $fillable = ['analisis_id', 'judul', 'deskripsi', 'prioritas', 'urutan'];
    public function analisis() { return $this->belongsTo(AnalisisKasus::class, 'analisis_id'); }
}

// ── app/Models/ConfidenceKasus.php
class ConfidenceKasus extends Model {
    protected $table = 'confidence_kasus';
    protected $fillable = ['analisis_id', 'kelengkapan_data', 'konsistensi_sumber', 'kualitas_dokumen', 'kedalaman_analisis'];
    public function analisis() { return $this->belongsTo(AnalisisKasus::class, 'analisis_id'); }
}

// ── app/Models/RiskAssessment.php
class RiskAssessment extends Model {
    protected $table = 'risk_assessment';
    protected $fillable = ['analisis_id', 'label', 'nilai', 'warna', 'keterangan', 'urutan'];
    public function analisis() { return $this->belongsTo(AnalisisKasus::class, 'analisis_id'); }
}