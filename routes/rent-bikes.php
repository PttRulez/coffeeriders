<?php

use App\Http\Controllers\BikeBookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BikeController;

Route::get('/rent-bikes', [BikeController::class, 'index'])->name('rent-bikes.index');
Route::get('/rent-bikes/{bike}', [BikeController::class, 'show'])->name('rent-bikes.show');
Route::get('/rent-bikes/category/{categoryName}', [BikeController::class, 'category'])->name('rent-bikes.category');

Route::post('/rent-bikes/{bike}/booking', [BikeBookingController::class, 'store'])->name('rent-bikes.booking.store');
