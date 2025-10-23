<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [BerandaController::class, 'index'])
        ->name('dashboard');

    Route::get('/terms', [BerandaController::class, 'terms'])
    ->name('terms');

    Route::resource('users', MahasiswaController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
