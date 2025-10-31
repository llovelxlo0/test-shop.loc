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
    public function index(Request $request) 
    {
        $query = Goods::query();

        //  Если выбран родитель
        if ($request->filled('parent_id')) {
            $parentId = $request->input('parent_id');

            // Находим все подкатегории этого родителя
            $childIds = Category::where('parent_id', $parentId)->pluck('id')->toArray();

            // Показываем товары из подкатегорий или самого родителя
            $query->whereIn('category_id', array_merge([$parentId], $childIds));
        }

        //  Если выбрана подкатегория
        if ($request->filled('subcategory_id')) {
            $query->where('category_id', $request->input('subcategory_id'));
        }

        $goods = $query->get();

        //  Формируем дерево категорий для фильтра
        $parents = Category::whereNull('parent_id')->get();
        $tree = [];
        foreach ($parents as $parent) {
            $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
        }

        //  Если запрос AJAX → возвращаем JSON (для JS фильтра)
        if ($request->ajax()) {
            return response()->json($goods);
        }

        //  Обычный HTML-рендер
        return view('Goods', compact('goods', 'tree'));
    }
    public function create(Request $request) 
    {
        $parents = $this->goodsService->getParentCategories();
        $selectedParentId = $request->filled('parent_id') ? $request->parent_id : old('parent_id');

        $childCategories = collect();
        $categoryAttributes = collect();

        if ($request->filled('parent_id')) {
        $childCategories = $this->goodsService->getChildCategories($request->parent_id);
        }

        if ($request->filled('category_id')) {
        $category = Category::with('attributes')->find($request->category_id);
        $categoryAttributes = $category?->attributes ?? collect();
        }
        return view('goods.create', compact('parents', 'childCategories', 'selectedParentId', 'categoryAttributes'));
    }
    public function store(GoodsRequest $request) 
    {
        $data = $request->validated();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
        }
        $this->goodsService->createGoods($data, $imagePath);
        return redirect()->route('goods.index')->with('success', 'Товар успешно создан.');
    }
    public function edit(Goods $good, Request $request) 
    {
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
        return redirect()->route('goods.index')->with('success', 'Товар успешно обновлен.');
    }
    public function destroy(Goods $good) 
    {
        $this->goodsService->deleteGoods($good);
        return redirect()->route('goods.index')->with('success', 'Товар успешно удален.');
    }
    public function getSubcategories($parentId)
    {
        $childCategories = app(\App\Services\CategoryService::class)->getChildCategories($parentId);
        return response()->json($childCategories);
    }

    public function FullInfo(Goods $goods) 
    {
        $goods = Goods::with('attributes')->findOrFail($goods->id);
        return view('goods.fullinfo', compact('goods')); 
    }

    public function goods(Request $request) 
    {
        // Родительские категории
    $parents = Category::whereNull('parent_id')->get();

    // Формируем дерево родитель → подкатегории
    $tree = [];
    foreach ($parents as $parent) {
        $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
    }

    // Фильтрация товаров
    $query = Goods::query();

    if ($request->ajax()) {
        // Если запрос AJAX — возвращаем JSON
        if ($request->filled('parent_id')) {
            $childIds = Category::where('parent_id', $request->parent_id)->pluck('id');
            $query->whereIn('category_id', $childIds);
        }
        if ($request->filled('subcategory_id')) {
            $query->where('category_id', $request->subcategory_id);
        }

        $goods = $query->get();
        return response()->json($goods);
    }

    // При обычной загрузке страницы — просто все товары
    $goods = Goods::latest()->get();

    return view('Goods', compact('tree', 'goods')); 
    }
    
}