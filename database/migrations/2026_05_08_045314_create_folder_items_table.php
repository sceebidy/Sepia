<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folder_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')
                  ->constrained('folders')
                  ->cascadeOnDelete();
            $table->enum('tipe', ['file', 'link', 'catatan']);
            $table->string('judul');

            // untuk link → simpan URL di sini
            // untuk catatan → simpan teks di sini
            $table->text('konten')->nullable();

            // khusus file
            $table->string('file_path')->nullable();
            $table->string('file_nama')->nullable();
            $table->string('file_tipe')->nullable();   // mime type: application/pdf, dll
            $table->unsignedBigInteger('file_ukuran')->nullable(); // dalam bytes

            // flag untuk n8n nanti
            $table->boolean('processed')->default(false);
            $table->text('hasil_rangkuman')->nullable(); // hasil AI n8n nanti

            $table->string('ditambahkan_oleh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folder_items');
    }
};