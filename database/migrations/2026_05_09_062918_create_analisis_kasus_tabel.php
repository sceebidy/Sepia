<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── ANALISIS KASUS (header)
        Schema::create('analisis_kasus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')
                  ->constrained('folders')
                  ->cascadeOnDelete();
            $table->string('judul');
            $table->dateTime('tanggal_analisis')->nullable();
            $table->decimal('tingkat_risiko', 3, 1)->nullable(); // 0.0 - 10.0
            $table->text('prediksi_vonis')->nullable();          // ← diubah: string → text (output AI bisa >255 char)
            $table->integer('jumlah_sumber')->default(0);
            $table->string('model_versi')->default('SEPIA v1.0');
            $table->timestamps();
        });

        // ── SWOT ITEMS
        Schema::create('swot_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')
                  ->constrained('analisis_kasus')
                  ->cascadeOnDelete();
            $table->enum('tipe', ['S', 'W', 'O', 'T']);
            $table->text('isi');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // ── AKTOR KASUS
        Schema::create('aktor_kasus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')
                  ->constrained('analisis_kasus')
                  ->cascadeOnDelete();
            $table->string('nama');
            $table->string('inisial', 10);   // ← diubah: 3 → 10 (AI bisa buat singkatan 4+ huruf, cth: BPID)
            $table->text('peran');            // ← diubah: string → text (peran bisa deskriptif panjang)
            $table->enum('status', ['tersangka', 'saksi', 'dpo']);
            $table->string('warna_avatar')->default('#1a5c2e');
            $table->timestamps();
        });

        // ── TIMELINE KASUS
        Schema::create('timeline_kasus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')
                  ->constrained('analisis_kasus')
                  ->cascadeOnDelete();
            $table->string('tanggal');       // string supaya fleksibel: "Jan 2021", "Mar–Des 2022"
            $table->text('keterangan');
            $table->string('warna_dot')->default('#16a34a');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // ── REKOMENDASI KASUS
        Schema::create('rekomendasi_kasus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')
                  ->constrained('analisis_kasus')
                  ->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('prioritas', ['tinggi', 'sedang', 'rendah'])->default('sedang');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // ── CONFIDENCE / KEPERCAYAAN ANALISIS
        Schema::create('confidence_kasus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')
                  ->constrained('analisis_kasus')
                  ->cascadeOnDelete();
            $table->integer('kelengkapan_data')->default(0);      // 0-100
            $table->integer('konsistensi_sumber')->default(0);
            $table->integer('kualitas_dokumen')->default(0);
            $table->integer('kedalaman_analisis')->default(0);
            $table->timestamps();
        });

        // ── RISK ASSESSMENT
        Schema::create('risk_assessment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')
                  ->constrained('analisis_kasus')
                  ->cascadeOnDelete();
            $table->string('label');          // cth: "Risiko Vonis Bebas"
            $table->integer('nilai');         // 0-100
            $table->string('warna')->default('#dc2626');
            $table->text('keterangan')->nullable();    // ← diubah: string → text (deskripsi risiko bisa panjang)
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_assessment');
        Schema::dropIfExists('confidence_kasus');
        Schema::dropIfExists('rekomendasi_kasus');
        Schema::dropIfExists('timeline_kasus');
        Schema::dropIfExists('aktor_kasus');
        Schema::dropIfExists('swot_items');
        Schema::dropIfExists('analisis_kasus');
    }
};