<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'nim' => '2008501001',
            'id_program_studi' => 1,
            'nama' => 'Admin DPM FMIPA',
            'angkatan' => 2021,
            'email' => 'dpmfmipaunud2025@gmail.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
        User::factory(100)->create();
    }
}
