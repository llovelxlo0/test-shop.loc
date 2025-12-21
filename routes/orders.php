<?php

use App\Http\Controllers\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/orders', [OrderController::class, 'index'])->name('orders.list');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
