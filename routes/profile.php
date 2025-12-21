<?php

use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\ProfileOrderController;
use Illuminate\Support\Facades\Route;


Route::prefix('profile')->middleware('auth')->group(function (){
    Route::get('/', [ProfileController::class, 'showProfile'])->name('profile');
    Route::put('/', [ProfileController::class, 'editProfile'])->name('profile.edit');
    Route::get('2fa/setup', [ProfileController::class, 'setupTwoFactor'])->name('2fa.setup');
    Route::post('2fa/verify', [ProfileController::class, 'verify'])->name('2fa.verifySetup');
    Route::delete('2fa/disable', [ProfileController::class, 'disable'])->name('2fa.disable');
});
Route::middleware('auth')->group(function (){
    Route::get('/profile/orders', [ProfileOrderController::class, 'index'])->name('profile.orders.index');
    Route::get('/profile/orders/{order}', [ProfileOrderController::class, 'show'])->name('profile.orders.show');
});
