<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analisis_kasus', function (Blueprint $table) {
            $table->text('ringkasan_eksekutif')->nullable()->after('prediksi_vonis');
            $table->text('deskripsi')->nullable()->after('ringkasan_eksekutif');
            $table->text('interpretasi')->nullable()->after('deskripsi');
            $table->text('ringkasan_intelijen')->nullable()->after('interpretasi');
            $table->text('konteks_geopolitik')->nullable()->after('ringkasan_intelijen');
            $table->text('catatan_analis')->nullable()->after('konteks_geopolitik');
            $table->string('klasifikasi_dokumen')->default('TERBATAS')->after('catatan_analis');
            $table->json('eei')->nullable()->after('klasifikasi_dokumen');
            $table->json('coa')->nullable()->after('eei');
            $table->json('early_warning')->nullable()->after('coa');
            $table->json('matrix_analisis')->nullable()->after('early_warning');
        });
    }

    public function down(): void
    {
        Schema::table('analisis_kasus', function (Blueprint $table) {
            $table->dropColumn([
                'ringkasan_eksekutif', 'deskripsi', 'interpretasi',
                'ringkasan_intelijen', 'konteks_geopolitik', 'catatan_analis',
                'klasifikasi_dokumen', 'eei', 'coa', 'early_warning', 'matrix_analisis',
            ]);
        });
    }
};