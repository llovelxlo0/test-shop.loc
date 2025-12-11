<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorLoginController;

// Гость: регистрация и авторизация
Route::middleware(['check2fa', 'guest' ])->group(function (){
    Route::get('register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('register', [RegisterController::class, 'processForm'])->name('register.process');

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'processLogin'])->name('login.process');
});

 // 2FA при входе
Route::get('/2fa/login', [TwoFactorLoginController::class, 'showVerifyForm'])->name('2fa.login.form');
Route::post('/2fa/login', [TwoFactorLoginController::class, 'verify'])->name('2fa.login.verify');

// Выход
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');