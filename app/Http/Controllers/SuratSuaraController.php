<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuratSuaraController extends Controller
{
    /**
     * Show vote page
     */
    public function show()
    {
        $user = User::where('nim', auth('web')->user()->nim)->first();
        
        // Get kegiatan BEM (fakultas level)
        $kegiatanBem = $user->kegiatan()
            ->where('tahun', date('Y'))
            ->where('ruang_lingkup', 'fakultas')
            ->with(['kandidat.mahasiswa'])
            ->first();
        
        // Get kegiatan HIMA (program studi level)
        $kegiatanHima = $user->kegiatan()
            ->where('tahun', date('Y'))
            ->where('ruang_lingkup', 'program studi')
            ->where('id_program_studi', $user->id_program_studi)
            ->with(['kandidat.mahasiswa'])
            ->first();
        
        // Check if user has valid ballot
        if (!$kegiatanBem || !$kegiatanHima) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki surat suara untuk pemilihan ini.');
        }
        
        // Check if user has already voted
        $hasvotedBem = $user->kegiatan()
            ->where('id_kegiatan', $kegiatanBem->id)
            ->wherePivot('has_vote', true)
            ->exists();
            
        $hasvotedHima = $user->kegiatan()
            ->where('id_kegiatan', $kegiatanHima->id)
            ->wherePivot('has_vote', true)
            ->exists();
        
        if ($hasvotedBem && $hasvotedHima) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda sudah melakukan pemilihan.');
        }

        return Inertia::render('Vote', [
            'kegiatanBem' => $kegiatanBem,
            'kegiatanHima' => $kegiatanHima,
        ]);
    }
}
