<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/contacts', fn () => Inertia::render('Contacts'))->name('contacts');

require __DIR__ . '/admin.php';
require __DIR__.'/auth.php';
require __DIR__.'/cycling-studio.php';
require __DIR__.'/rent-bikes.php';
