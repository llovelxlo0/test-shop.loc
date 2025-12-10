<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TwoFactorLoginController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Models\Goods;
use App\Models\Review;

// Главная страница
Route::get('/', function () {
    return view('Home');
})->name('home');

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

// Категории и товары (открыты для всех)
Route::resource('categories', CategoryController::class);
Route::get('goods/{goods}/info', [GoodsController::class, 'FullInfo'])->name('goods.info');
Route::resource('goods', GoodsController::class);

// Корзина и заказы
Route::get('cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::get('/orders', [OrderController::class, 'listOrders'])->name('orders.list');
Route::get('/orders/{order}', [OrderController::class, 'viewOrder'])->name('orders.view');

// Профиль + 2FA + список желаемого
Route::prefix('profile')->middleware(['auth', 'check2fa'])->group(function (){
    Route::get('/', [ProfileController::class, 'showProfile'])->name('profile');
    Route::put('/', [ProfileController::class, 'editProfile'])->name('profile.edit');
    Route::get('2fa/setup', [ProfileController::class, 'setupTwoFactor'])->name('2fa.setup');
    Route::post('2fa/verify', [ProfileController::class, 'verify'])->name('2fa.verifySetup');
    Route::delete('2fa/disable', [ProfileController::class, 'disable'])->name('2fa.disable');
});

// Список желаемого
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'showWishlist'])->name('wishlist.show');
    Route::post('/wishlist/add/{goods}', [WishlistController::class, 'addToWishlist'])->name('wishlist.add');
    Route::post('/wishlist/remove/{goods}', [WishlistController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::post('wishlist/{goods}', [WishlistController::class, 'toggleWishlist'])->name('wishlist.toggle');
});

// Отзывы
Route::middleware('auth')->group(function (){
    Route::post('goods/{goods}/reviews', [ReviewController::class, 'store'])->name('goods.reviews.store');
    Route::get('reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});
