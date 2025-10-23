<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [BerandaController::class, 'index'])
        ->name('dashboard');

    Route::resource('users', MahasiswaController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('events', KegiatanController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
