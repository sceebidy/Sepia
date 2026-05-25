<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analisis_kasus', function (Blueprint $table) {
            $table->text('analisis_intelijen')->nullable()->after('catatan_analis');
        });
    }

    public function down(): void
    {
        Schema::table('analisis_kasus', function (Blueprint $table) {
            $table->dropColumn('analisis_intelijen');
        });
    }
};  