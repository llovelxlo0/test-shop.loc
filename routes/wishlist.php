<?php

use App\Http\Controllers\Wishlist\WishlistController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist/{goods}', [WishlistController::class, 'toggleWishlist'])->name('wishlist.toggle');
});
