<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('rent-bikes.index');
})->name('home');


require __DIR__ . '/admin.php';
require __DIR__.'/auth.php';
require __DIR__.'/rent-bikes.php';
