<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Order\OrderController;
use Illuminate\Support\Facades\Route;


Route::get('cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('checkout', [OrderController::class, 'checkout'])->name('checkout');
