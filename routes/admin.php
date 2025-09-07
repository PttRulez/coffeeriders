<?php

use App\Http\Controllers\Admin\AdminBikeBookingController;
use App\Http\Controllers\Admin\AdminBikeController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('adminka')->middleware('admin')->name('adminka.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::resource('rent-bikes', AdminBikeController::class)->parameters(['rent-bikes' => 'bike']);
    Route::delete('bikes/{bike}/images/{image}', [AdminBikeController::class, 'destroyImage'])->name('rent-bikes.images.destroy');
    
    Route::delete('/rent-bikes/booking/{bikeBooking}', [AdminBikeBookingController::class, 'destroy'])->name('rent-bikes.booking.destroy');
    
});