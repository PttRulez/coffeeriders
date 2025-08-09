<?php

use App\Http\Controllers\BikeBookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BikeRentController;

Route::get('/rent-bikes', [BikeRentController::class, 'index'])->name('rent-bikes.index');
Route::get('/rent-bikes/{bike}', [BikeRentController::class, 'show'])->name('rent-bikes.show');
Route::get('/rent-bikes/category/{categoryName}', [BikeRentController::class, 'category'])->name('rent-bikes.category');

Route::post('/rent-bikes/{bike}/booking', [BikeBookingController::class, 'store'])->name('rent-bikes.booking.store');
