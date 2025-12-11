<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TwoFactorLoginController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Models\Goods;
use App\Models\Review;

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