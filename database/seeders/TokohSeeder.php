<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TokohSeeder extends Seeder
{
    public function run(): void
    {
        $tokoh = [
            [
                'nama'       => 'Ahmad Rasyid',
                'inisial'    => 'AR',
                'kategori'   => 'ideologi',
                'peran'      => 'Tokoh Ideologi',
                'wilayah'    => 'Jawa Timur',
                'risiko'     => 'tinggi',
                'afiliasi'   => 'Kelompok X',
                'catatan'    => 'Aktif merekrut anggota baru di lingkungan pesantren.',
                'status'     => 'aktif',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama'       => 'Budi Santoso',
                'inisial'    => 'BS',
                'kategori'   => 'politik',
                'peran'      => 'Figur Politik',
                'wilayah'    => 'Jakarta',
                'risiko'     => 'sedang',
                'afiliasi'   => 'Partai Y',
                'catatan'    => 'Terdeteksi memiliki komunikasi dengan aktor asing tidak resmi.',
                'status'     => 'aktif',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama'       => 'D. Kurniawan',
                'inisial'    => 'DK',
                'kategori'   => 'hankam',
                'peran'      => 'Aktor Siber',
                'wilayah'    => 'Tidak Diketahui',
                'risiko'     => 'tinggi',
                'afiliasi'   => 'Tidak Diketahui',
                'catatan'    => 'Diduga terlibat dalam serangan siber terhadap infrastruktur pemerintah.',
                'status'     => 'aktif',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama'       => 'Hendra Wijaya',
                'inisial'    => 'HW',
                'kategori'   => 'ekonomi',
                'peran'      => 'Pengusaha',
                'wilayah'    => 'Kalimantan',
                'risiko'     => 'sedang',
                'afiliasi'   => 'PT. Z',
                'catatan'    => 'Terkait dugaan aliran dana ilegal proyek infrastruktur.',
                'status'     => 'aktif',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama'       => 'Rini Kusuma',
                'inisial'    => 'RK',
                'kategori'   => 'sosbud',
                'peran'      => 'Aktivis',
                'wilayah'    => 'Sulawesi Tengah',
                'risiko'     => 'rendah',
                'afiliasi'   => 'LSM A',
                'catatan'    => 'Terlibat dalam mediasi konflik komunal.',
                'status'     => 'aktif',
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama'       => 'Farhan Malik',
                'inisial'    => 'FM',
                'kategori'   => 'ideologi',
                'peran'      => 'Penyebar Konten',
                'wilayah'    => 'Jawa Barat',
                'risiko'     => 'tinggi',
                'afiliasi'   => 'Kelompok X',
                'catatan'    => 'Aktif menyebarkan konten radikal di platform terenkripsi.',
                'status'     => 'aktif',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('tokoh')->insert($tokoh);
    }
};