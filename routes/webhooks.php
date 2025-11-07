<?php

use App\Http\Controllers\TinkoffPaymentController;

Route::prefix('tinkoff')->group(function () {
    Route::post('/bike-rent-notification/{bikeBooking}', [TinkoffPaymentController::class,
        'handleBikeBookingNotificationFromBank'])->name('tinkoff.handle-bike-booking-notification');
    Route::post('/cycling-studio-notification', [TinkoffPaymentController::class,
        'handleCyclingNotificationFromBank'])->name('tinkoff.handle-cycling-studio-notification');
});
