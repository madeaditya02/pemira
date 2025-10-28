<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [BerandaController::class, 'index'])
        ->name('dashboard');

    Route::get('/terms', [BerandaController::class, 'terms'])
    ->name('terms');

    Route::get('/resultHima', [BerandaController::class, 'resultHima'])
    ->name('resultHima');

    Route::get('/cakabem', [BerandaController::class, 'cakabem'])
    ->name('cakabem');

    Route::resource('users', MahasiswaController::class)
        ->only(['index', 'store', 'update', 'destroy']);

<<<<<<< HEAD
    Route::post('users/sync-data/{year}', [MahasiswaController::class, 'syncMahasiswa'])
        ->name('users.sync-data');

    Route::resource('events', KegiatanController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('candidates', KandidatController::class)
        ->only(['index', 'store', 'update', 'destroy']);
=======
    Route::get('/cakahima', [BerandaController::class, 'cakahima'])
    ->name('cakahima');
>>>>>>> df0cfdb432a435db61796765036780222afaaeda
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
