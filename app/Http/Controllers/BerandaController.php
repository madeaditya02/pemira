<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Kegiatan;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BerandaController extends Controller
{
    private function getProgramStudi()
    {
        $user = auth('web')->user();
        return $user->id_program_studi ?? null;
    }

    public function getTime()
    {
        $kegiatan = Kegiatan::all();
        $uniqueWaktuMulai = $kegiatan->pluck('waktu_mulai')->unique();

        if ($uniqueWaktuMulai->count() === 1) {
            $waktu = $uniqueWaktuMulai->first();
        } else {
            $currentYearKegiatan = $kegiatan->where('tahun', now()->year);
            $waktu = $currentYearKegiatan->pluck('waktu_mulai')->unique()->first();
        }

        return $waktu;
    }

    public function guest()
    {
        return Inertia::render('Dashboard', [
            'waktu' => $this->getTime(),
        ]);
    }

    public function index()
    {
        return Inertia::render('Dashboard', [
            'kegiatan' => Kegiatan::all(),
            'waktu' => $this->getTime(),
        ]);
    }

    public function terms()
    {
        return Inertia::render('Terms', [
            'kegiatan' => Kegiatan::all(),
            'waktu' => $this->getTime(),
        ]);
    }

    public function candidates(string $slug)
    {
        $kegiatan = Kegiatan::where('nama', str_replace('-', ' ', $slug))
            ->with('kandidat.mahasiswa')
            ->firstOrFail();
        $kandidat = Kandidat::where('id_kegiatan', $kegiatan->id)
            ->with('mahasiswa')
            ->get();

        $idProdi = $this->getProgramStudi();
        if ($idProdi !== $kegiatan->id_program_studi && $kegiatan->ruang_lingkup === 'program studi') {
            return redirect()->back();
        }

        return Inertia::render('Candidates', [
            'kegiatan' => $kegiatan,
            'kandidat' => $kandidat,
        ]);
    }

    public function cakabem()
    {
        return Inertia::render('PilihCakaBem', [
            'kegiatan' => Kegiatan::all(),
            'waktu' => $this->getTime(),
        ]);
    }

    public function cakahima()
    {
        return Inertia::render('PilihCakaHima', [
            'kegiatan' => Kegiatan::all(),
            'waktu' => $this->getTime(),
        ]);
    }

    public function resultHima()
    {
        return Inertia::render('ResultHima');
    }
}
