<?php

use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', function () {
    return view('Home');
})->name('home');

require __DIR__.'/categories.php';
require __DIR__.'/goods.php';
require __DIR__.'/cart.php';
require __DIR__.'/orders.php';
require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
require __DIR__.'/reviews.php';
require __DIR__.'/wishlist.php';
require __DIR__.'/admin.php';