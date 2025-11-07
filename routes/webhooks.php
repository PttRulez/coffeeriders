<?php

use App\Http\Controllers\TinkoffPaymentController;

Route::prefix('tinkoff')->group(function () {
    Route::post('/bike-rent-notification/{bikeBooking}', [TinkoffPaymentController::class,
        'handleBikeBookingNotificationFromBank'])->name('tinkoff.handle-bike-booking-notification');
    Route::post('/cycling-order-notification', [TinkoffPaymentController::class,
        'handleCyclingOrderNotificationFromBank'])->name('tinkoff.handle-cycling-order-notification');
    Route::post('/cycling-activity-notification', [TinkoffPaymentController::class,
        'handleCyclingActivityNotificationFromBank'])->name('tinkoff.handle-cycling-activity-notification');
});
