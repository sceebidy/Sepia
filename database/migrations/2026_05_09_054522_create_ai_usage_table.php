<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage', function (Blueprint $table) {
            $table->id();

            // Relasi ke item yang diproses
            $table->foreignId('folder_item_id')
                  ->constrained('folder_items')
                  ->cascadeOnDelete();

            // Status proses
            $table->enum('status', ['pending', 'proses', 'selesai', 'gagal'])
                  ->default('pending');

            // Info model AI yang dipakai
            $table->string('model')->default('gpt-4o');

            // Token usage (untuk monitoring biaya)
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();

            // Performa
            $table->unsignedInteger('durasi_detik')->nullable();

            // Hasil mentah dari AI (JSON string)
            $table->text('hasil_raw')->nullable();

            // Error jika gagal
            $table->text('error_message')->nullable();

            // Siapa yang memicu
            $table->string('dipicu_oleh')->default('C. Rasyid');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage');
    }
};