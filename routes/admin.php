<?php

use App\Http\Controllers\Admin\AdminBikeBookingController;
use App\Http\Controllers\Admin\AdminBikeController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCyclingController;
use App\Http\Controllers\Admin\AdminImageController;
use App\Http\Controllers\Admin\AdminRaceController;
use App\Http\Controllers\Admin\AdminWorkshopCategoryController;
use App\Http\Controllers\Admin\AdminWorkshopServiceController;
use App\Http\Controllers\Admin\AdminUsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('adminka')->middleware('admin')->name('adminka.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    //      BLOG
    Route::resource('blog', AdminBlogController::class);
    Route::patch('blog/{blog}/toggle-published', [AdminBlogController::class, 'togglePublished'])
        ->name('blog.toggle-published');
    
    //      CYCLING STUDIO
    Route::resource('cycling-studio', AdminCyclingController::class)->except(['edit'])->parameters(['cycling-studio' => 'activity']);
    Route::post('/cycling-studio/bike-check', [AdminCyclingController::class, 'bikeCheck'])
        ->name('cycling-studio.bike-check');
    
    //      RENT BIKES
    Route::prefix('rent-bikes')->name('rent-bikes.')->group(function () {
        Route::resource('bookings', AdminBikeBookingController::class)
            ->only(['index', 'create', 'store', 'destroy'])
            ->parameters(['bookings' => 'bikeBooking']);
    });
    Route::resource('rent-bikes', AdminBikeController::class)->parameters(['rent-bikes' => 'bike']);
    Route::delete('bikes/{bike}/images/{image}',
        [AdminBikeController::class, 'destroyImage'])->name('rent-bikes.images.destroy');
    
    //      USERS
    Route::resource('users', AdminUsersController::class)->only(['index', 'edit', 'update']);
    Route::put('/users/{user}/cycling-activities-count', [AdminUsersController::class, 'updateCyclingActivitiesCount'])
        ->name('users.update-cycling-activities-count');
    Route::put('/users/{user}/is-coffeerider', [AdminUsersController::class, 'updateIsCoffeeRider'])
        ->name('users.update-is-coffeerider');
    
    //      COUPONS
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::patch('coupons/{coupon}/toggle-active', [AdminCouponController::class, 'toggleActive'])
        ->name('coupons.toggle-active');

    //      RACES
    Route::resource('races', AdminRaceController::class);

    //      WORKSHOP SERVICES
    Route::resource('workshop-services', AdminWorkshopServiceController::class)->except(['show']);
    Route::resource('workshop-categories', AdminWorkshopCategoryController::class)->except(['show']);
    
    // IMAGES
    Route::post('/upload-image', [AdminImageController::class, 'upload'])
        ->name('image.upload');
    Route::delete('/delete-image', [AdminImageController::class, 'destroy'])
        ->name('image.destroy');
});
