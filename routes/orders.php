<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/orders', [OrderController::class, 'listOrders'])->name('orders.list');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
