<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('rent-bikes');
})->name('home');

Route::get('/rent-bikes', function () {
    return Inertia::render('rent-bikes/Index');
})->name('rent-bikes');

Route::fallback(function () {
    return redirect()->route('rent-bikes');
});

//Route::get('dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//require __DIR__.'/settings.php';
//require __DIR__.'/auth.php';
