<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewModerationController;
use App\Http\Controllers\ReviewVoteController;


Route::middleware('auth')->group(function (){
    Route::post('goods/{goods}/reviews', [ReviewController::class, 'store'])->name('goods.reviews.store');
    Route::get('reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

Route::middleware(['auth'])->prefix('reviews')->name('reviews.')->group(function() {
    Route::post('{review}/approve', [ReviewModerationController::class, 'approve'])->name('approve');
    Route::post('{review}/reject', [ReviewModerationController::class, 'reject'])->name('reject');
});

Route::middleware('auth')
    ->post('reviews/{review}/vote', [ReviewVoteController::class, 'vote'])
    ->name('reviews.vote');