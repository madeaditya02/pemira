<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\SuratSuaraController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::controller(BerandaController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
        Route::get('terms', 'terms')->name('terms');
        Route::get('candidates/{slug}', 'candidates')->name('candidates');
        Route::get('cakabem', 'cakabem')->name('cakabem');
        Route::get('cakahima', 'cakahima')->name('cakahima');
        Route::get('result-hima', 'resultHima')->name('result-hima');
    });

    Route::controller(SuratSuaraController::class)->group(function () {
        Route::get('vote', 'show')->name('vote.show');
    });

    Route::resource('users', MahasiswaController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::post('users/sync-data/{year}', [MahasiswaController::class, 'syncMahasiswa'])
        ->name('users.sync-data');

    Route::resource('events', KegiatanController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('candidates', KandidatController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
