<?php  

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsController;

Route::get('/goods', [GoodsController::class, 'index'])->name('goods.index');
Route::get('goods/{goods}/info', [GoodsController::class, 'FullInfo'])->name('goods.info');

Route::middleware('auth')->group(function () {
    Route::get('/goods/create', [GoodsController::class, 'create'])->name('goods.create');
    Route::post('/goods', [GoodsController::class, 'store'])->name('goods.store');
    Route::get('/goods/{goods}/edit', [GoodsController::class, 'edit'])->name('goods.edit');
    Route::put('/goods/{goods}', [GoodsController::class, 'update'])->name('goods.update');
    Route::delete('/goods/{goods}', [GoodsController::class, 'destroy'])->name('goods.destroy');
});
