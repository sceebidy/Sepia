<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanAnalisisSeeder extends Seeder
{
    public function run(): void
    {
        // ── LAPORAN
        $laporan = [
            ['judul' => 'Laporan Mingguan Ideologi - Minggu 1 Mei 2026',    'kategori' => 'ideologi', 'status' => 'final',  'dibuat_oleh' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()],
            ['judul' => 'Laporan Situasi Politik Nasional April 2026',       'kategori' => 'politik',  'status' => 'final',  'dibuat_oleh' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(2), 'updated_at' => Carbon::now()],
            ['judul' => 'Laporan Ancaman Siber Q1 2026',                    'kategori' => 'hankam',   'status' => 'final',  'dibuat_oleh' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(3), 'updated_at' => Carbon::now()],
            ['judul' => 'Laporan Korupsi Proyek Infrastruktur Kalimantan',  'kategori' => 'ekonomi',  'status' => 'draft',  'dibuat_oleh' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()],
            ['judul' => 'Laporan Konflik Komunal Sulawesi Tengah',          'kategori' => 'sosbud',   'status' => 'final',  'dibuat_oleh' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()],
            ['judul' => 'Laporan Aktivitas Vessel Asing ZEE Natuna',       'kategori' => 'hankam',   'status' => 'dikirim','dibuat_oleh' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(6), 'updated_at' => Carbon::now()],
            ['judul' => 'Laporan Disinformasi Pilkada 2026',               'kategori' => 'politik',  'status' => 'final',  'dibuat_oleh' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(7), 'updated_at' => Carbon::now()],
        ];

        DB::table('laporan')->insert($laporan);

        // ── ANALISIS
        $analisis = [
            ['judul' => 'Analisis Jaringan Radikalisme Jawa Timur',         'kategori' => 'ideologi', 'issue_id' => 1, 'status' => 'selesai', 'analis' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(0), 'updated_at' => Carbon::now()],
            ['judul' => 'Analisis Pola Disinformasi Elektoral',             'kategori' => 'politik',  'issue_id' => 8, 'status' => 'selesai', 'analis' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()],
            ['judul' => 'Analisis Aliran Dana Proyek Kalimantan',          'kategori' => 'ekonomi',  'issue_id' => 14,'status' => 'proses',  'analis' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(2), 'updated_at' => Carbon::now()],
            ['judul' => 'Analisis Serangan Siber Infrastruktur Kritis',    'kategori' => 'hankam',   'issue_id' => 21,'status' => 'selesai', 'analis' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(3), 'updated_at' => Carbon::now()],
            ['judul' => 'Analisis Sentimen Hoaks SARA di Media Sosial',    'kategori' => 'sosbud',   'issue_id' => 19,'status' => 'proses',  'analis' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()],
            ['judul' => 'Analisis Jaringan Separatis Papua',               'kategori' => 'ideologi', 'issue_id' => 2, 'status' => 'selesai', 'analis' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()],
            ['judul' => 'Analisis Pendanaan Kampanye Asing',               'kategori' => 'politik',  'issue_id' => 12,'status' => 'proses',  'analis' => 'C. Rasyid', 'created_at' => Carbon::now()->subDays(6), 'updated_at' => Carbon::now()],
        ];

        DB::table('analisis')->insert($analisis);
    }
};