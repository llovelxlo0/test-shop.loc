<?php

use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TwoFactorLoginController;


Route::get('/', function () {
    return view('Home');
})->name('home');
    Route::get('register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('register', [RegisterController::class, 'processForm'])->name('register.process');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::get('goods/{goods}/info', [GoodsController::class, 'FullInfo'])->name('goods.info');
    Route::get('/2fa/login', [TwoFactorLoginController::class, 'showVerifyForm'])->name('2fa.login.form');
    Route::post('/2fa/login', [TwoFactorLoginController::class, 'verify'])->name('2fa.login.verify');
    Route::resource('/categories', CategoryController::class);
    Route::resource('/goods', GoodsController::class);
    Route::get('cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('order/confirmation/{order}', [OrderController::class, 'confirmation'])->name('order.confirmation');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');




Route::middleware('auth')->group(function () {
    Route::get('/profile/2fa/setup', [ProfileController::class, 'setupTwoFactor'])->name('2fa.setup');
    Route::post('/profile/2fa/verify', [ProfileController::class, 'verify'])->name('2fa.verifySetup');
    Route::delete('/profile/2fa/disable', [ProfileController::class, 'disable'])->name('2fa.disable');
    
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'editProfile'])->name('profile.edit');
    
    
    
});

