<?php

namespace Database\Seeders;

use App\Models\Kandidat;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KandidatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kandidat::factory()->createMany([
            [
                'id_kegiatan' => 1,
                'no_urut' => '01',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 1,
                'no_urut' => '02',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 2,
                'no_urut' => '01',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 2,
                'no_urut' => '02',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 3,
                'no_urut' => '01',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 3,
                'no_urut' => '02',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 4,
                'no_urut' => '01',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 4,
                'no_urut' => '02',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 5,
                'no_urut' => '01',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 5,
                'no_urut' => '02',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 6,
                'no_urut' => '01',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 6,
                'no_urut' => '02',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 7,
                'no_urut' => '01',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
            [
                'id_kegiatan' => 7,
                'no_urut' => '02',
                'visi' => 'Mewujudkan Pemira yang transparan dan akuntabel.',
                'misi' => '1. Meningkatkan partisipasi mahasiswa dalam Pemira. 2. Menyediakan informasi yang jelas dan akurat mengenai calon kandidat. 3. Meningkatkan kualitas debat kandidat.',
            ],
        ]);

        // Proses setiap kandidat yang telah dibuat
        Kandidat::all()->each(function ($kandidat) {
            // Ambil kegiatan dari kandidat yang dibuat
            $id_kegiatan = $kandidat->id_kegiatan;
            $kegiatan = Kegiatan::find($id_kegiatan);

            // Cek berdasarkan ruang lingkup kegiatan
            switch ($kegiatan->ruang_lingkup) {
                case 'fakultas':
                    // Ambil 2 mahasiswa secara acak dari tabel mahasiswa sebagai calon ketua dan wakil
                    $mahasiswa = User::inRandomOrder()->limit(2)->get();
                    $jabatan = ['ketua', 'wakil'];
                    foreach ($mahasiswa as $index => $mhs) {
                        $kandidat->mahasiswa()->attach($mhs->nim, [
                            'jabatan' => $jabatan[$index],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    break;
                case 'program studi':
                    // Ambil 1 mahasiswa secara acak dengan program studi yang sama sebagai calon ketua
                    $mahasiswa = User::where('id_program_studi', $kegiatan->id_program_studi)
                        ->whereNotIn('nim', function ($query) {
                            $query->select('nim')
                                ->from('mahasiswa_kandidat');
                        })
                        ->get()
                        ->random(1)
                        ->first();
                    if ($mahasiswa) {
                        $kandidat->mahasiswa()->attach($mahasiswa->nim, [
                            'jabatan' => 'ketua',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    break;
            }
        });
    }
}
