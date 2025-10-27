<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [BerandaController::class, 'index'])
        ->name('dashboard');

    Route::resource('users', MahasiswaController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::post('users/sync-data/{year}', [MahasiswaController::class, 'syncMahasiswa'])
        ->name('users.sync-data');

    Route::resource('events', KegiatanController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('candidates', KandidatController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
