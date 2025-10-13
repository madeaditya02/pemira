<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'mahasiswa';

    protected $primaryKey = 'nim';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nim',
        'id_program_studi',
        'nama',
        'angkatan',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi');
    }

    public function kandidat()
    {
        return $this->belongsToMany(Kandidat::class, 'mahasiswa_kandidat', 'nim', 'id_kandidat')
            ->withPivot('jabatan');
    }

    public function kegiatan()
    {
        return $this->belongsToMany(Kegiatan::class, 'surat_suara', 'nim', 'id_kegiatan');
    }
}
