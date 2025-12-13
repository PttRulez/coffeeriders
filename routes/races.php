<?php

use App\Http\Controllers\RaceController;
use Illuminate\Support\Facades\Route;

Route::get('/races/{race}', [RaceController::class, 'show'])->name('races.show');

Route::middleware(['authed'])->group(function () {
    Route::post('/races/{race}/clusters/{cluster}/register', [RaceController::class, 'register'])
        ->name('races.register');
});
