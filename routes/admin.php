<?php

use App\Http\Controllers\Admin\AdminBikeRentController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('adminka')->middleware('admin')->name('adminka.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::resource('rent-bikes', AdminBikeRentController::class)->parameters(['rent-bikes' => 'bike']);
});