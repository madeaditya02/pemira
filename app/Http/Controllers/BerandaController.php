<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BerandaController extends Controller
{
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
}
