<?php

use App\Http\Controllers\CyclingStudioController;
use Illuminate\Support\Facades\Route;

Route::get('/cycling-studio', [CyclingStudioController::class, 'index'])->name('cycling-studio.index');
Route::get('/cycling-studio/booking', [CyclingStudioController::class, 'booking'])->name('cycling-studio.booking');

Route::middleware(['authed'])->group(function () {
    Route::post('/cycling-studio/booking', [CyclingStudioController::class, 'storeBooking'])
        ->name('cycling-studio.booking.store');

    Route::post('/cycling-studio/bike-check', [CyclingStudioController::class, 'bikeCheck'])
        ->name('cycling-studio.bike-check');
});