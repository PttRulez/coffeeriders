<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/miniapp', function () {
    return Inertia::render('miniapp/Index');
});