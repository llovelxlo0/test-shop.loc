<?php

use App\Http\Controllers\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::resource('categories', CategoryController::class);
Route::get('/categories/{parent}/subcategories', [CategoryController::class, 'getSubcategories']);

