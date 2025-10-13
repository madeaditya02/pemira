<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kegiatan::factory()->createMany([
            [
                'nama' => 'Pemilihan Caka Cawaka BEM FMIPA',
                'tahun' => 2025,
                'waktu_mulai' => '2025-11-12 06:00:00',
                'waktu_selesai' => '2025-11-12 18:00:00',
                'ruang_lingkup' => 'fakultas',
                'id_program_studi' => null,
            ],
            [
                'nama' => 'Pemilihan Caka HIMAKI',
                'tahun' => 2025,
                'waktu_mulai' => '2025-11-12 06:00:00',
                'waktu_selesai' => '2025-11-12 18:00:00',
                'ruang_lingkup' => 'program studi',
                'id_program_studi' => 1,
            ],
            [
                'nama' => 'Pemilihan Caka HIMAFI',
                'tahun' => 2025,
                'waktu_mulai' => '2025-11-12 06:00:00',
                'waktu_selesai' => '2025-11-12 18:00:00',
                'ruang_lingkup' => 'program studi',
                'id_program_studi' => 2,
            ],
            [
                'nama' => 'Pemilihan Caka HIMABIO',
                'tahun' => 2025,
                'waktu_mulai' => '2025-11-12 06:00:00',
                'waktu_selesai' => '2025-11-12 18:00:00',
                'ruang_lingkup' => 'program studi',
                'id_program_studi' => 3,
            ],
            [
                'nama' => 'Pemilihan Caka HIMATIKA',
                'tahun' => 2025,
                'waktu_mulai' => '2025-11-12 06:00:00',
                'waktu_selesai' => '2025-11-12 18:00:00',
                'ruang_lingkup' => 'program studi',
                'id_program_studi' => 4,
            ],
            [
                'nama' => 'Pemilihan Caka HIMAFARMA',
                'tahun' => 2025,
                'waktu_mulai' => '2025-11-12 06:00:00',
                'waktu_selesai' => '2025-11-12 18:00:00',
                'ruang_lingkup' => 'program studi',
                'id_program_studi' => 5,
            ],
            [
                'nama' => 'Pemilihan Caka HIMAIF',
                'tahun' => 2025,
                'waktu_mulai' => '2025-11-12 06:00:00',
                'waktu_selesai' => '2025-11-12 18:00:00',
                'ruang_lingkup' => 'program studi',
                'id_program_studi' => 6,
            ],
        ]);
    }
}
