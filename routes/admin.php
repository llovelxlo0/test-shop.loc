<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\ReviewModerationController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (){
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/reviews/{review}/approve', [ReviewModerationController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [ReviewModerationController::class, 'reject'])->name('reviews.reject');
    Route::resource('categories', CategoryController::class);
    Route::resource('goods', GoodsController::class)->except(['index']);
    // Route::get('/goods/create', [GoodsController::class, 'create'])->name('goods.create');
    // Route::post('/goods', [GoodsController::class, 'store'])->name('goods.store');
    // Route::get('/goods/{good}/edit', [GoodsController::class, 'edit'])->name('goods.edit');
    // Route::put('/goods/{good}', [GoodsController::class, 'update'])->name('goods.update');
    // Route::delete('/goods/{good}', [GoodsController::class, 'destroy'])->name('goods.destroy');
});