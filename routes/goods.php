<?php  

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsController;

Route::get('goods/{goods}/info', [GoodsController::class, 'FullInfo'])->name('goods.info');
Route::resource('goods', GoodsController::class);