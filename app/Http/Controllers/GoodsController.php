<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Goods;
use App\Services\GoodsService;
use App\Http\Requests\GoodsRequest;

use Illuminate\Http\Request;

class GoodsController extends Controller
{
    protected $goodsService;
    public function __construct(GoodsService $goodsService)
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->goodsService = $goodsService;
    }
    public function index() 
    {
        $goods = Goods::with('category')->paginate(15);
        return view('goods.index', compact('goods' )); // categories/index.blade.php
    }
    public function create(Request $request) 
    {
        $parents = $this->goodsService->getParentCategories();

        $selectedParentId = $request->filled('parent_id') ? $request->parent_id : old('parent_id');

        $childCategories = collect();
        if ($request->filled('parent_id')) {
            $childCategories = $this->goodsService->getChildCategories($request->parent_id);
        }
        return view('goods.create', compact('parents', 'childCategories', 'selectedParentId'));
    }
    public function store(GoodsRequest $request) 
    {
        $data = $request->validated();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
        }
        $this->goodsService->createGoods($data, $imagePath);
        return redirect()->route('goods.index')->with('success', 'Категория успешно создана.');
    }
    public function edit(Goods $good, Request $request) 
    {
        //$goods = Goods::findOrFail($id);
        $parents = $this->goodsService->getParentCategories();

        $selectedParentId = $request->filled('parent_id') ? $request->parent_id : old('parent_id', $good->category ? $good->category->parent_id : null);

        $childCategories = collect();
        if ($request->filled('parent_id')) {
            $childCategories = $this->goodsService->getChildCategories($request->parent_id);
        } elseif ($good->category && $good->category->parent_id) {
            $childCategories = $this->goodsService->getChildCategories($good->parent_id);
        }
        return view('goods.edit', compact('good', 'parents', 'childCategories', 'selectedParentId'));
    }
    public function update(Goods $good, GoodsRequest $request) 
    {
        $data = $request->validated();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
        }
        $this->goodsService->updateGoods($good, $data, $imagePath);
        return redirect()->route('goods.index')->with('success', 'Категория успешно обновлена.');
    }
    public function destroy(Goods $good) 
    {
        $this->goodsService->deleteGoods($good);
        return redirect()->route('goods.index')->with('success', 'Категория успешно удалена.');
    }
    public function FullInfo(Goods $goods) 
    {
        return view('goods.fullinfo', compact('goods')); // goods/info.blade.php
    }

    public function goods() 
    {
        $parents = Category::whereNull('parent_id')->get();

        $tree = [];
        foreach ($parents as $parent) {
            $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
        }
        return view('Goods', compact('tree')); // categories/goods.blade.php
    }
}