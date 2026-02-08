<?php

use App\Http\Controllers\GeneralController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\TinkoffPaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $races = \App\Models\Race::published()->upcoming()->orderBy('date')->get();
    return Inertia::render('Home', ['races' => $races]);
})->name('home');
Route::get('/contacts', fn() => Inertia::render('Contacts'))->name('contacts');
Route::post('/feedback', [GeneralController::class, 'feedBackForm'])->name('feedback-form');
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Route::post('/pricing-preview', [PricingController::class, 'preview'])->name('pricing.preview');

require __DIR__ . '/admin.php';
require __DIR__ . '/blog.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/cycling-studio.php';
require __DIR__ . '/races.php';
require __DIR__ . '/rent-bikes.php';
require __DIR__ . '/tinkoff.php';
require __DIR__ . '/user-account.php';
