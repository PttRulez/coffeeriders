<?php

use App\Http\Controllers\TinkoffPaymentController;

Route::prefix('tinkoff')->group(function () {
    Route::get('/success-payment', [TinkoffPaymentController::class, 'successPayment'])
        ->name('success-payment');
    Route::get('/failed-payment', [TinkoffPaymentController::class, 'failedPayment'])
        ->name('failed-payment');
});
