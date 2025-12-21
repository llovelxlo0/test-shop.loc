<?php

use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Goods\GoodsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ReviewModerationController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (){
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/reviews/{review}/approve', [ReviewModerationController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [ReviewModerationController::class, 'reject'])->name('reviews.reject');
    Route::resource('categories', CategoryController::class);
    Route::resource('goods', GoodsController::class)->except(['index']);

});
