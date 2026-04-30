<?php

use App\Http\Controllers\Admin\AdminBikeBookingController;
use App\Http\Controllers\Admin\AdminBikeController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCyclingController;
use App\Http\Controllers\Admin\AdminImageController;
use App\Http\Controllers\Admin\AdminRaceController;
use App\Http\Controllers\Admin\AdminWorkshopRepairOrderController;
use App\Http\Controllers\Admin\AdminWorkshopCategoryController;
use App\Http\Controllers\Admin\AdminWorkshopSparePartCategoryController;
use App\Http\Controllers\Admin\AdminWorkshopSparePartPurchaseController;
use App\Http\Controllers\Admin\AdminWorkshopSparePartController;
use App\Http\Controllers\Admin\AdminWorkshopServiceController;
use App\Http\Controllers\Admin\AdminUsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('adminka')->middleware('authed')->name('adminka.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])
        ->middleware('mechanic')
        ->name('index');

        Route::middleware('admin')->group(function () {
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
        Route::patch('bikes/{bike}/images/{image}/primary',
            [AdminBikeController::class, 'setPrimaryImage'])->name('rent-bikes.images.set-primary');

        //      USERS
        Route::resource('users', AdminUsersController::class)->only(['index', 'edit', 'update']);
        Route::put('/users/{user}/cycling-activities-count', [AdminUsersController::class, 'updateCyclingActivitiesCount'])
            ->name('users.update-cycling-activities-count');
        Route::put('/users/{user}/is-coffeerider', [AdminUsersController::class, 'updateIsCoffeeRider'])
            ->name('users.update-is-coffeerider');
        Route::put('/users/{user}/is-mechanic', [AdminUsersController::class, 'updateIsMechanic'])
            ->name('users.update-is-mechanic');

        //      COUPONS
        Route::resource('coupons', AdminCouponController::class)->except(['show']);
        Route::patch('coupons/{coupon}/toggle-active', [AdminCouponController::class, 'toggleActive'])
            ->name('coupons.toggle-active');

        //      RACES
        Route::resource('races', AdminRaceController::class);

        //      WORKSHOP (admin only)
            Route::prefix('workshop')->name('workshop.')->group(function () {
                Route::get(
                    'repair-orders/report',
                    [AdminWorkshopRepairOrderController::class, 'report'],
                )->name('repair-orders.report');
                Route::resource('services', AdminWorkshopServiceController::class)
                    ->except(['show'])
                    ->parameters(['services' => 'workshopService']);
            Route::resource('categories', AdminWorkshopCategoryController::class)
                ->except(['show'])
                ->parameters(['categories' => 'workshopCategory']);
            Route::resource('spare-parts', AdminWorkshopSparePartController::class)
                ->parameters(['spare-parts' => 'workshopSparePart']);
            Route::resource('spare-part-categories', AdminWorkshopSparePartCategoryController::class)
                ->except(['show'])
                ->parameters(['spare-part-categories' => 'workshopSparePartCategory']);
            Route::get(
                'spare-part-purchases',
                [AdminWorkshopSparePartPurchaseController::class, 'index'],
            )->name('spare-part-purchases.index');
            Route::resource('spare-parts.purchases', AdminWorkshopSparePartPurchaseController::class)
                ->except(['show', 'index'])
                ->parameters([
                    'spare-parts' => 'workshopSparePart',
                    'purchases' => 'workshopSparePartPurchase',
                ]);
        });

        // IMAGES
        Route::post('/upload-image', [AdminImageController::class, 'upload'])
            ->name('image.upload');
        Route::delete('/delete-image', [AdminImageController::class, 'destroy'])
            ->name('image.destroy');
        
        
    });

    Route::prefix('workshop')->name('workshop.')->middleware('mechanic')->group(function () {
        Route::resource('repair-orders', AdminWorkshopRepairOrderController::class)
            ->except(['show'])
            ->parameters(['repair-orders' => 'workshopRepairOrder']);
        Route::patch(
            'repair-orders/{workshopRepairOrder}/status',
            [AdminWorkshopRepairOrderController::class, 'updateStatus'],
        )->name('repair-orders.update-status');
    });
});
