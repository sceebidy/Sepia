<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', [
                'ideologi',
                'politik',
                'ekonomi',
                'sosbud',
                'hankam',
            ]);
            $table->enum('sub_kategori', [
                // Ideologi
                'radikalisme',
                'separatisme',
                'ekstremisme',
                // Politik
                'elektoral',
                'intervensi_asing',
                'oposisi',
                // Ekonomi
                'korupsi',
                'investasi_asing',
                'pencucian_uang',
                // Sosbud
                'hoaks_sara',
                'komunal',
                'budaya',
                // Hankam
                'siber',
                'terorisme',
                'perbatasan',
            ]);
            $table->enum('risiko', ['tinggi', 'sedang', 'rendah']);
            $table->enum('status', ['aktif', 'selesai', 'arsip'])->default('aktif');
            $table->string('wilayah')->nullable();
            $table->string('sumber')->nullable();
            $table->timestamps(); // created_at dipakai untuk tren 7 hari & recent items
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};