<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten')->nullable();
            $table->enum('kategori', [
                'ideologi',
                'politik',
                'ekonomi',
                'sosbud',
                'hankam',
                'umum',
            ]);
            $table->foreignId('issue_id')           // relasi ke tabel issues (opsional)
                  ->nullable()
                  ->constrained('issues')
                  ->nullOnDelete();
            $table->enum('status', ['proses', 'selesai'])->default('proses');
            $table->string('analis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis');
    }
};