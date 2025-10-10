<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Goods;

use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function info(Goods $goods) {
        return view('categories.Goodsinfo', compact('goods')); // goods/info.blade.php
    }

    public function goods() {
        view()->composer('Layouts.app', function ($view) {

        $parents = Category::whereNull('parent_id')->get();

        $tree = [];
        foreach ($parents as $parent) {
            $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
        }
        //dd($tree);
        return view('Goods', compact('tree'));
        });
    }
}