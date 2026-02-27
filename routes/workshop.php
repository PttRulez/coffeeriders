<?php

use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

Route::get('/workshop', [WorkshopController::class, 'index'])->name('workshop.index');
Route::get('/workshop/pricelist', [WorkshopController::class, 'index'])->name('workshop.pricelist');
