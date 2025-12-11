<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;


Route::middleware('auth')->group(function (){
    Route::post('goods/{goods}/reviews', [ReviewController::class, 'store'])->name('goods.reviews.store');
    Route::get('reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});