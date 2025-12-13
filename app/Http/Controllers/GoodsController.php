<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Goods;
use App\Services\RelatedProductService;
use App\Services\ViewHistoryService;
use App\Http\Requests\GoodsRequest;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CategoryService;
use App\Services\GoodsFilterService;
use App\Services\GoodsCrudService;
use App\Services\GoodsAttributesService;

class GoodsController extends Controller
{
    protected $relatedProductService;
    protected $viewHistoryService;
    protected $categoryService;
    protected $goodsFilterService;
    protected $goodsAttributesService;
    protected $goodsCrudService;
    public function __construct(
    RelatedProductService $relatedProductService, 
    ViewHistoryService $viewHistoryService, 
    CategoryService $categoryService,
    GoodsFilterService $goodsFilterService,
    GoodsAttributesService $goodsAttributesService,
    GoodsCrudService $goodsCrudService)
    {
        $this->relatedProductService = $relatedProductService;
        $this->categoryService = $categoryService;
        $this->viewHistoryService = $viewHistoryService;
        $this->goodsFilterService = $goodsFilterService;
        $this->goodsAttributesService = $goodsAttributesService;
        $this->goodsCrudService = $goodsCrudService;
    }

        public function index(Request $request)
    {
        $this->authorize('viewAny', Goods::class);
        $data = $this->goodsFilterService->getFilteredData($request);

        // AJAX-запросы — только JSON с товарами
        if ($request->ajax()) {
            return response()->json($data['goods']);
        }

        return view('Goods', [
            'goods'              => $data['goods'],
            'tree'               => $data['tree'],
            'attributesForFilter'=> $data['attributesForFilter'],
            'selectedAttributes' => $data['selectedAttributes'],
        ]);
    }

    // Дерево категорий: родитель → подкатегории.
    protected function buildCategoryTree(): array
    {
        $parents = Category::whereNull('parent_id')->get();

        $tree = [];
        foreach ($parents as $parent) {
            $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
        }

        return $tree;
    }

    // Построение атрибутов для фильтрации с учетом выбранных категорий
    protected function buildAttributesForFilter(?int $parentId, ?int $subcategoryId)
    {
        
        $categoryIdsForFilter = Goods::query()
            ->when($parentId, function ($q) use ($parentId) {
                $childIds = Category::where('parent_id', $parentId)->pluck('id')->toArray();
                $q->whereIn('category_id', array_merge([$parentId], $childIds));
            })
            ->when($subcategoryId, function ($q) use ($subcategoryId) {
                $q->where('category_id', $subcategoryId);
            })
            ->distinct()
            ->pluck('category_id');

        $attributes = Attribute::whereHas('goods', function ($q) use ($categoryIdsForFilter) {
            if ($categoryIdsForFilter->isNotEmpty()) {
                $q->whereIn('goods.category_id', $categoryIdsForFilter);
            }
        })->get();

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

        return $attributesForFilter;
    }

        
    public function create(Request $request) 
    {
        $this->authorize('create', Goods::class);

        $parents = $this->categoryService->getParentCategories();
        $selectedParentId = $request->filled('parent_id') ? $request->parent_id : old('parent_id');

        $childCategories = collect();
        $categoryAttributes = collect();

        if ($request->filled('parent_id')) {
        $childCategories = $this->categoryService->getChildCategories($request->parent_id);
        }

        if ($request->filled('category_id')) {
        $category = Category::with('attributes')->find($request->category_id);
        $categoryAttributes = $category?->attributes()->get() ?? collect();
        }
        return view('goods.create', compact('parents', 'childCategories', 'selectedParentId', 'categoryAttributes'));
    }
    public function store(GoodsRequest $request) 
    {
        $this->authorize('create', Goods::class);

        $data = $request->validated();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
        }
        $good = $this->goodsCrudService->create($data, $imagePath);
        $this->goodsAttributesService->syncAttributes($good, $data['attributes'] ?? []);
        return redirect()->route('goods.index')->with('success', 'Товар успешно создан.');
    }
    public function edit(Goods $good, Request $request) 
    {
        $this->authorize('update', $good);

        $parents = $this->categoryService->getParentCategories();
        $selectedParentId = $request->filled('parent_id') ? $request->parent_id : old('parent_id', $good->category ? $good->category->parent_id : null);
        $childCategories = collect();
        if ($request->filled('parent_id')) {
            $childCategories = $this->categoryService->getChildCategories($request->parent_id);
        } elseif ($good->category && $good->category->parent_id) {
            $childCategories = $this->categoryService->getChildCategories($good->category->parent_id);
        }
        return view('goods.edit', compact('good', 'parents', 'childCategories', 'selectedParentId'));
    }
    public function update(Goods $good, GoodsRequest $request) 
    {
        $this->authorize('update', $good);

        $data = $request->validated();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
        }
        $good = $this->goodsCrudService->update($good, $data, $imagePath);
        $this->goodsAttributesService->syncAttributes($good, $data['attributes'] ?? []);
        return redirect()->route('goods.index')->with('success', 'Товар успешно обновлен.');
    }
    public function destroy(Goods $good) 
    {
        $this->authorize('delete', $good);

        $this->goodsCrudService->delete($good);
        return redirect()->route('goods.index')->with('success', 'Товар успешно удален.');
    }
    public function getSubcategories($parentId)
    {
        $childCategories = app(\App\Services\CategoryService::class)->getChildCategories($parentId);
        return response()->json($childCategories);
    }

    public function FullInfo(Goods $goods) 
    {
        $sort = request('sort', 'date');
        $goods->load([
            'attributes' => function ($q) {
                $q->withPivot('value');
            },
            'category.parent',
            'reviews.user',
        ]);

        $reviewsQuery = $goods->reviews()->visible()->with('user');

        if ($sort === 'rating') {
            $reviewsQuery
        ->withSum('votes as votes_sum', 'value') // посчитает SUM(value) по связи vote
        ->orderByDesc('votes_sum')  // алиас формируется как: имя_связи + '_sum'
        ->orderByDesc('created_at');
        } else {
            $reviewsQuery->orderByDesc('created_at');
        }
        $reviews = $reviewsQuery->get();

        $this->viewHistoryService->add($goods);
        $viewHistory = $this->viewHistoryService
            ->get()
            ->where('id', '!=', $goods->id);

        $relatedGoods = $this->relatedProductService->getRelatedProducts($goods);

        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = Auth::user()->wishlist()->where('goods_id', $goods->id)->exists();
        }

        return view('goods.fullinfo', [
            'goods'        => $goods,
            'relatedGoods' => $relatedGoods ?? collect(),
            'viewHistory'  => $viewHistory,
            'isInWishlist' => $isInWishlist,
            'reviews' => $reviews,
            'sort' => $sort,
        ]); 
    }
}