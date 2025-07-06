<?php

use App\Http\Controllers\BikeRentController;
use App\Models\Bike;
use App\Services\BikeRentService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return redirect()->route('rent-bikes');
})->name('home');

Route::get('/rent-bikes', [BikeRentController::class, 'index'])->name('rent-bikes');

//Route::get('dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//require __DIR__.'/settings.php';

require __DIR__ . '/admin.php';
require __DIR__.'/auth.php';
