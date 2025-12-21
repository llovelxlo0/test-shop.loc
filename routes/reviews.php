<?php

use App\Http\Controllers\Review\ReviewController;
use App\Http\Controllers\Review\ReviewReplyController;
use App\Http\Controllers\Review\ReviewVoteController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function (){
    Route::post('goods/{goods}/reviews', [ReviewController::class, 'store'])->name('goods.reviews.store');
    Route::get('reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('reviews/{review}/replies', [ReviewReplyController::class, 'store'])->name('reviews.replies.store');
    Route::get('review-replies/{reply}/edit', [ReviewReplyController::class, 'edit'])->name('reviews.replies.edit');
    Route::put('review-replies/{reply}', [ReviewReplyController::class, 'update'])->name('reviews.replies.update');
    Route::delete('review-replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('reviews.replies.destroy');
});

Route::middleware('auth')
    ->post('reviews/{review}/vote', [ReviewVoteController::class, 'vote'])
    ->name('reviews.vote');
