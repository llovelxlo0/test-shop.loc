<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Goods;
use App\Services\GoodsService;
use App\Services\RelatedProductService;
use App\Services\ViewHistoryService;
use App\Http\Requests\GoodsRequest;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    protected $goodsService;
    protected $relatedProductService;
    protected $viewHistoryService;
    public function __construct(GoodsService $goodsService, RelatedProductService $relatedProductService, ViewHistoryService $viewHistoryService)
    {
        $this->middleware('auth')->except(['index', 'show', 'FullInfo']);
        $this->goodsService = $goodsService;
        $this->relatedProductService = $relatedProductService;
        $this->viewHistoryService = $viewHistoryService;
    }
  public function index(Request $request)
{
    $query = Goods::query();

    // 1. Фильтр по категориям
    if ($request->filled('parent_id')) {
        $parentId = (int) $request->input('parent_id');
        $childIds = Category::where('parent_id', $parentId)
            ->pluck('id')
            ->toArray();

        $query->whereIn('category_id', array_merge([$parentId], $childIds));
    }

    if ($request->filled('subcategory_id')) {
        $query->where('category_id', (int) $request->input('subcategory_id'));
    }

    // 2. Фильтр по EAV-атрибутам
    // из формы приходит массив вида:
    // attributes[attribute_id] = [value1, value2, ...]
    $attributeFilters = $request->input('attributes', []);

    if (!empty($attributeFilters)) {
        $query->where(function ($q) use ($attributeFilters) {
            foreach ($attributeFilters as $attrId => $values) {
                $values = array_filter((array) $values);

                // пропускаем атрибут, если не выбрали ни одного значения
                if (empty($values)) {
                    continue;
                }

                $q->whereHas('attributes', function ($qa) use ($attrId, $values) {
                    $qa->where('attributes.id', $attrId)
                       ->whereIn('attribute_values.value', $values);
                });
            }
        });
    }

    // товары после всех фильтров
    $goods = $query->get();

    // 3. Дерево категорий для селектов
    $parents = Category::whereNull('parent_id')->get();
    $tree = [];
    foreach ($parents as $parent) {
        $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
    }

    // 4. Какие категории участвуют в выборке (для построения списка атрибутов)
    $categoryIdsForFilter = Goods::query()
        ->when($request->filled('parent_id'), function ($q) use ($request) {
            $parentId = (int) $request->input('parent_id');
            $childIds = Category::where('parent_id', $parentId)
                ->pluck('id')
                ->toArray();

            $q->whereIn('category_id', array_merge([$parentId], $childIds));
        })
        ->when($request->filled('subcategory_id'), function ($q) use ($request) {
            $q->where('category_id', (int) $request->input('subcategory_id'));
        })
        ->distinct()
        ->pluck('category_id');

    // 5. Атрибуты, которые реально есть у товаров этих категорий
    $attributes = Attribute::whereHas('goods', function ($q) use ($categoryIdsForFilter) {
            if ($categoryIdsForFilter->isNotEmpty()) {
                $q->whereIn('goods.category_id', $categoryIdsForFilter);
            }
        })
        ->get();

    // 6. Для каждого атрибута получаем список возможных значений
    $attributesForFilter = $attributes->map(function ($attr) use ($categoryIdsForFilter) {
        $valuesQuery = DB::table('attribute_values')
            ->where('attribute_id', $attr->id);

        if ($categoryIdsForFilter->isNotEmpty()) {
            $valuesQuery
                ->join('goods', 'goods.id', '=', 'attribute_values.goods_id')
                ->whereIn('goods.category_id', $categoryIdsForFilter);
        }

        $attr->filter_values = $valuesQuery
            ->distinct()
            ->pluck('value')
            ->sort()
            ->values();

        return $attr;
    });

    // 7. Если это AJAX — отдаем только товары
    if ($request->ajax()) {
        return response()->json($goods);
    }

    // 8. Обычный рендер
    return view('Goods', [
        'goods'              => $goods,
        'tree'               => $tree,
        'attributesForFilter'=> $attributesForFilter,
        'selectedAttributes' => $attributeFilters,
    ]);
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
        $categoryAttributes = $category?->attributes()->get() ?? collect();
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
            $childCategories = $this->goodsService->getChildCategories($good->category->parent_id);
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
        $this->viewHistoryService->add($goods);
        $viewHistory = $this->viewHistoryService->get()->where('id', '!=', $goods->id);
        $goods = Goods::with(['attributes', 'category', 'reviews.user'])->findOrFail($goods->id);
        $relatedGoods = $this->relatedProductService->getRelatedProducts($goods);
        return view('goods.fullinfo', [
            'goods' => $goods,
            'relatedGoods' => $relatedGoods ?? collect(),
            'viewHistory' => $viewHistory
        ]); 
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