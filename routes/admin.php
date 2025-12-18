<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\ReviewModerationController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (){
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::post('/reviews/{review}/approve', [ReviewModerationController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [ReviewModerationController::class, 'reject'])->name('reviews.reject');
    Route::resource('categories', CategoryController::class);
    Route::resource('goods', GoodsController::class)->except(['index']);

});