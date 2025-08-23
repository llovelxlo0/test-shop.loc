<?php

use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('Home');
});
Route::get('register', [RegisterController::class, 'showForm'])->name('register');
Route::post('register', [RegisterController::class, 'processForm'])->name('register.process');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'processLogin'])->name('login.process');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

