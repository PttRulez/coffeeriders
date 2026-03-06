<?php

use App\Http\Controllers\RaceController;
use Illuminate\Support\Facades\Route;

Route::get('/races/calendar/{year?}', [RaceController::class, 'calendar'])
    ->where('year', '[0-9]{4}')
    ->name('races.calendar');

Route::get('/races/{race}', [RaceController::class, 'show'])->name('races.show');

Route::middleware(['authed'])->group(function () {
    Route::post('/races/{race}/participate', [RaceController::class, 'participate'])
        ->name('races.participate');

    Route::post('/races/{race}/clusters/{cluster}/register', [RaceController::class, 'register'])
        ->name('races.register');
});
