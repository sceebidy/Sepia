<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokoh', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('inisial', 3);          // untuk avatar, contoh: AR, BS, DK
            $table->enum('kategori', [
                'ideologi',
                'politik',
                'ekonomi',
                'sosbud',
                'hankam',
            ]);
            $table->string('peran')->nullable();    // contoh: Tokoh Ideologi, Figur Politik, Aktor Siber
            $table->string('wilayah')->nullable();  // contoh: Jawa Timur, Jakarta, Tidak Diketahui
            $table->enum('risiko', ['tinggi', 'sedang', 'rendah']);
            $table->string('afiliasi')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['aktif', 'arsip'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokoh');
    }
};