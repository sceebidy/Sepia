<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('ringkasan')->nullable();
            $table->enum('kategori', [
                'ideologi',
                'politik',
                'ekonomi',
                'sosbud',
                'hankam',
                'umum',
            ]);
            $table->enum('status', ['draft', 'final', 'dikirim'])->default('draft');
            $table->string('dibuat_oleh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};