<?php

use App\Http\Controllers\TinkoffPaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('Home'))->name('home');
Route::get('/contacts', fn() => Inertia::render('Contacts'))->name('contacts');


Route::get('/tinkoff/success-payment', [TinkoffPaymentController::class, 'successPayment'])->name('success-payment');
Route::get('/tinkoff/failed-payment', [TinkoffPaymentController::class, 'failedPayment'])->name('failed-payment');
Route::post('/tinkoff/init-rent-bike-payment', [TinkoffPaymentController::class, 'initRentBikePayment'])->name('tinkoff.init-rent-bike-payment');
Route::post('/tinkoff/notification-url', [TinkoffPaymentController::class, 'handleNotificationFromBank'])->name('tinkoff.handle-notification-from-bank');

require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/cycling-studio.php';
require __DIR__ . '/rent-bikes.php';
require __DIR__ . '/user-account.php';
