<?php

use App\Http\Controllers\CyclingStudioController;
use Illuminate\Support\Facades\Route;

Route::get('/cycling-studio', [CyclingStudioController::class, 'index'])->name('cycling-studio.index');