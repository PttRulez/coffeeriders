<?php

use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/user-account', [UserAccountController::class, 'index'])->name('user-account.index');
    Route::post('/user-account/set-new-password', [UserAccountController::class, 'updatePassword'])->name('user-account.update-password');
});
