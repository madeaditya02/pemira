<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
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

        // Check user requirements
        if ($user->is_admin) {
            return redirect()->back()->with('alert', ['type' => 'error', 'title' => 'Anda adalah Admin', 'message' => 'Admin tidak dapat mengakses halaman pemilihan.']);
        }
        if (!$user->isActive()) {
            return redirect()->back()->with('alert', ['type' => 'error', 'title' => 'Akun Tidak Aktif', 'message' => 'Anda tidak terdaftar sebagai mahasiswa aktif sehingga tidak dapat melakukan pemilihan.']);
        }

        // Get kegiatan BEM (fakultas level)
        $kegiatanBem = Kegiatan::where('tahun', date('Y'))
            ->where('waktu_selesai', '>', now())
            ->where('ruang_lingkup', 'fakultas')
            ->with(['kandidat.mahasiswa', 'programStudi'])
            ->first();

        // Get kegiatan HIMA (program studi level)
        $kegiatanHima = Kegiatan::where('tahun', date('Y'))
            ->where('waktu_selesai', '>', now())
            ->where('ruang_lingkup', 'program studi')
            ->where('id_program_studi', $user->id_program_studi)
            ->with(['kandidat.mahasiswa', 'programStudi'])
            ->first();

        // 1. Check if kegiatan exists
        if (!$kegiatanBem || !$kegiatanHima) {
            return redirect()->route('dashboard')
                ->with('alert', ['type' => 'error', 'title' => 'Tidak Ada Kegiatan Pemilihan', 'message' => 'Saat ini tidak ada kegiatan pemilihan yang aktif.']);
        }

        // 2. Check if user has valid ballot (surat suara)
        $hasBallotBem = $user->kegiatan()
            ->where('id_kegiatan', $kegiatanBem->id)
            ->exists();

        $hasBallotHima = $user->kegiatan()
            ->where('id_kegiatan', $kegiatanHima->id)
            ->exists();

        if (!$hasBallotBem || !$hasBallotHima) {
            return redirect()->route('dashboard')
                ->with('alert', ['type' => 'error', 'title' => 'Tidak Ada Surat Suara', 'message' => 'Anda tidak memiliki surat suara untuk pemilihan ini.']);
        }

        // 3. Check if kegiatan has started
        if (now()->lt($kegiatanBem->waktu_mulai) || now()->lt($kegiatanHima->waktu_mulai)) {
            return redirect()->route('dashboard')
                ->with('alert', ['type' => 'error', 'title' => 'Pemilihan Belum Dimulai', 'message' => 'Kegiatan pemilihan belum dimulai. Silakan coba lagi nanti.']);
        }

        // 4. Check if user has already voted
        $hasVotedBem = $user->kegiatan()
            ->where('id_kegiatan', $kegiatanBem->id)
            ->wherePivot('has_vote', true)
            ->exists();

        $hasVotedHima = $user->kegiatan()
            ->where('id_kegiatan', $kegiatanHima->id)
            ->wherePivot('has_vote', true)
            ->exists();

        if ($hasVotedBem && $hasVotedHima) {
            return redirect()->route('dashboard')
                ->with('alert', ['type' => 'error', 'title' => 'Pemilihan Sudah Dilakukan', 'message' => 'Anda hanya dapat melakukan pemilihan sekali. Terima kasih telah berpartisipasi.']);
        }

        return Inertia::render('Vote', [
            'kegiatanBem' => $kegiatanBem,
            'kegiatanHima' => $kegiatanHima,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kandidat_bem' => 'required|exists:kandidat,id',
            'id_kandidat_hima' => 'required|exists:kandidat,id',
        ]);

        $kandidatBem = Kandidat::find($request->id_kandidat_bem);
        $kandidatHima = Kandidat::find($request->id_kandidat_hima);

        $user = User::where('nim', auth('web')->user()->nim)->first();
        $user->kegiatan()->syncWithoutDetaching([$kandidatBem->id_kegiatan => ['has_vote' => true]]);
        $user->kegiatan()->syncWithoutDetaching([$kandidatHima->id_kegiatan => ['has_vote' => true]]);

        $kandidatBem->increment('jumlah_suara');
        $kandidatHima->increment('jumlah_suara');

        return redirect()->route('dashboard')
            ->with('alert', ['type' => 'success', 'title' => 'Pemilihan Berhasil', 'message' => 'Terima kasih telah berpartisipasi dalam pemilihan.']);
    }
}
