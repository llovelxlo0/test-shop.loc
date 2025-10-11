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


Route::get('/', function () {
    return view('Home');
})->name('home');
    Route::get('register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('register', [RegisterController::class, 'processForm'])->name('register.process');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');




Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::get('goods', [GoodsController::class, 'goods'])->name('goods');
    Route::put('profile', [ProfileController::class, 'editProfile'])->name('profile.edit');
    
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/goods/{goods}', [GoodsController::class, 'info'])->name('goods.info');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
});

