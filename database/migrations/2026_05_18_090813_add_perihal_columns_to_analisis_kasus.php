<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analisis_kasus', function (Blueprint $table) {
            $table->string('perihal')->nullable()->after('judul');
            $table->string('periode')->nullable()->after('perihal');
            $table->string('wilayah')->nullable()->after('periode');
            $table->json('jabatan_rekomendasi')->nullable()->after('wilayah');
            $table->json('fakta_fakta')->nullable()->after('jabatan_rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('analisis_kasus', function (Blueprint $table) {
            $table->dropColumn(['perihal', 'periode', 'wilayah', 'jabatan_rekomendasi', 'fakta_fakta']);
        });
    }
};