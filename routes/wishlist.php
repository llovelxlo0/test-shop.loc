<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WishlistController;


Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'showWishlist'])->name('wishlist.show');
    Route::post('/wishlist/add/{goods}', [WishlistController::class, 'addToWishlist'])->name('wishlist.add');
    Route::post('/wishlist/remove/{goods}', [WishlistController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::post('wishlist/{goods}', [WishlistController::class, 'toggleWishlist'])->name('wishlist.toggle');
});