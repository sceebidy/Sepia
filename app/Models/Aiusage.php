<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsage extends Model
{
    protected $table = 'ai_usage';

    protected $fillable = [
        'folder_item_id',
        'status',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'durasi_detik',
        'hasil_raw',
        'error_message',
        'dipicu_oleh',
    ];

    protected $casts = [
        'prompt_tokens'      => 'integer',
        'completion_tokens'  => 'integer',
        'total_tokens'       => 'integer',
        'durasi_detik'       => 'integer',
    ];

    // ── Relasi ──────────────────────────────────
    public function folderItem()
    {
        return $this->belongsTo(FolderItem::class, 'folder_item_id');
    }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'ai_usage_id');
    }

    // ── Helper: badge warna status ───────────────
    public function statusColor(): array
    {
        return match ($this->status) {
            'selesai' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
            'proses'  => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
            'gagal'   => ['bg' => '#fff5f5', 'text' => '#dc2626', 'border' => '#fca5a5'],
            default   => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'border' => '#d1d5db'], // pending
        };
    }

    // ── Helper: format total token ───────────────
    public function tokenFormatted(): string
    {
        if (!$this->total_tokens) return '-';
        return number_format($this->total_tokens) . ' token';
    }

    // ── Helper: estimasi biaya (GPT-4o pricing) ──
    public function estimasiBiaya(): string
    {
        if (!$this->total_tokens) return '-';
        // GPT-4o: ~$5/1M input tokens, ~$15/1M output tokens (approx)
        $biaya = (($this->prompt_tokens ?? 0) * 0.000005)
               + (($this->completion_tokens ?? 0) * 0.000015);
        return '$' . number_format($biaya, 4);
    }
}