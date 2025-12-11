<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;


Route::prefix('profile')->middleware(['auth', 'check2fa'])->group(function (){
    Route::get('/', [ProfileController::class, 'showProfile'])->name('profile');
    Route::put('/', [ProfileController::class, 'editProfile'])->name('profile.edit');
    Route::get('2fa/setup', [ProfileController::class, 'setupTwoFactor'])->name('2fa.setup');
    Route::post('2fa/verify', [ProfileController::class, 'verify'])->name('2fa.verifySetup');
    Route::delete('2fa/disable', [ProfileController::class, 'disable'])->name('2fa.disable');
});