<?php

use App\Http\Controllers\Admin\AdminBikeBookingController;
use App\Http\Controllers\Admin\AdminBikeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCyclingController;
use App\Http\Controllers\Admin\AdminUsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('adminka')->middleware('admin')->name('adminka.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    Route::get('cycling-studio', [AdminCyclingController::class, 'index'])->name('cycling-studio.index');
    Route::delete('cycling-studio/activities/{cyclingActivity}',
        [AdminCyclingController::class, 'destroy'])->name('cycling-studio.activities.destroy');
    Route::put('cycling-studio/activities/{cyclingActivity}',
        [AdminCyclingController::class, 'update'])->name('cycling-studio.activities.update');
    
    Route::get('rent-bikes/bookings', [AdminBikeBookingController::class, 'index'])->name('rent-bikes.bookings');
    Route::delete('rent-bikes/bookings/{bikeBooking}',
        [AdminBikeBookingController::class, 'destroy'])->name('rent-bikes.booking.destroy');
    Route::resource('rent-bikes', AdminBikeController::class)->parameters(['rent-bikes' => 'bike']);
    Route::delete('bikes/{bike}/images/{image}',
        [AdminBikeController::class, 'destroyImage'])->name('rent-bikes.images.destroy');
    
    Route::get('users', [AdminUsersController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/cycling-activities-count', [AdminUsersController::class, 'updateCyclingActivitiesCount'])
        ->name('users.update-cycling-activities-count');
    Route::put('/users/{user}/is-coffeerider', [AdminUsersController::class, 'updateIsCoffeeRider'])
        ->name('users.update-is-coffeerider');
    
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::patch('coupons/{coupon}/toggle-active', [AdminCouponController::class, 'toggleActive'])
            ->name('coupons.toggle-active');
});