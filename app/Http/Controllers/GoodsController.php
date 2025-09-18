<?php

namespace App\Http\Controllers;
use App\Models\Goods;

use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function info(Goods $goods) {
        return view('categories.Goodsinfo', compact('goods')); // goods/info.blade.php
    }
}
