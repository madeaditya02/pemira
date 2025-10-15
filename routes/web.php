<?php

use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [BerandaController::class, 'index'])
        ->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
