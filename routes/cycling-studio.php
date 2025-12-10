<?php

use App\Http\Controllers\CyclingStudioController;
use Illuminate\Support\Facades\Route;

Route::resource('cycling-studio', CyclingStudioController::class)->only(['index', 'create']);

Route::middleware(['authed'])->group(function () {
    Route::resource('cycling-studio', CyclingStudioController::class)->only(['destroy', 'store', 'edit', 'update'])
        ->parameters([
            'cycling-studio' => 'activity',
        ]);
    
    Route::post('/cycling-studio/buy-activities', [CyclingStudioController::class, 'buyActivities'])
        ->name('cycling-studio.buy-activities');
    
    Route::post('/cycling-studio/bike-check', [CyclingStudioController::class, 'bikeCheck'])
        ->name('cycling-studio.bike-check');
});