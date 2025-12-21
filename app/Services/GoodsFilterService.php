<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsFilterService
{
    /**
     * Основной метод: применяет фильтры и готовит данные для страницы каталога.
     */
    public function getFilteredData(Request $request): array
    {
        $query = Goods::query()
            ->with([
                'category:id,name,parent_id',
            ]);

        $parentId      = $request->integer('parent_id');       // родительская категория
        $subcategoryId = $request->integer('subcategory_id');  // подкатегория
        $attributeFilters = $request->input('attributes', []); // фильтр по EAV

        /*
         * 1. Фильтр по категориям
         */
        if ($parentId) {
            $childIds = Category::where('parent_id', $parentId)->pluck('id')->toArray();
            $query->whereIn('category_id', array_merge([$parentId], $childIds));
        }
        if ($subcategoryId) {
            $query->where('category_id', $subcategoryId);
        }

        /*
         * 2. Фильтр по EAV-атрибутам
         * Формат: attributes[attribute_id] = [value1, value2, ...]
         */
        if (!empty($attributeFilters)) {
            foreach ($attributeFilters as $attributeId => $values) {
                $values = array_filter((array) $values);
                if (empty($values)) {
                    continue;
                }
                $query->whereHas('attributes', function ($q) use ($attributeId, $values) {
                   $q->where('attributes.id', $attributeId)->whereIn('attribute_values.value', $values);
                });
            }
        }

        // Товары по всем фильтрам
        $goods = $query->paginate(12)->withQueryString();

        /*
         * 3. Дерево категорий для выпадающего списка
         */
        $tree = $this->buildCategoryTree();

        /*
         * 4. Список категорий, которые сейчас участвуют в выборке
         * (чтобы не показывать в фильтре атрибуты, которых нет в конкретной категории)
         */
        $categoryIdsForFilter = Goods::query()->when($parentId, function ($q, $parentId) {
           $childIds = Category::where('parent_id', $parentId)->pluck('id')->toArray();
           $q->whereIn('category_id', array_merge([$parentId], $childIds));
        })
            ->when($subcategoryId, function ($q) use ($subcategoryId){
                $q->where('category_id', $subcategoryId);
            })->distinct()->pluck('category_id');

        /*
         * 5. Атрибуты, которые реально есть у товаров этих категорий
         */
        $attributes = Attribute::whereHas('goods', function ($q) use ($categoryIdsForFilter) {
            if ($categoryIdsForFilter->isNotEmpty()) {
                $q->whereIn('goods.category_id', $categoryIdsForFilter);
            }
        })->get();

        /*
         * 6. Для каждого атрибута — набор возможных значений (для checkboxes)
         */
        $attributeValues = DB::table('attribute_values')
            ->select('attribute_id', 'value')
            ->when($categoryIdsForFilter->isNotEmpty(), function ($q) use ($categoryIdsForFilter){
                $q->join('goods', 'goods.id', '=', 'attribute_values.goods_id')
                    ->whereIn('goods.category_id', $categoryIdsForFilter);
            })->distinct()->get()->groupBy('attribute_id');

        $attributesForFilter = $attributes->map(function ($attr) use ($attributeValues){
            $attr->filter_values = $attributeValues[$attr->id]?->pluck('value')->sort()->values() ?? collect();
            return $attr;
        });

        // Готовим результат
        return [
            'goods'              => $goods,
            'tree'               => $tree,
            'attributesForFilter'=> $attributesForFilter,
            'selectedAttributes' => $attributeFilters,
        ];
    }

    /**
     * Строим дерево категорий: [parentName => [childId => childName]]
     */
    protected function buildCategoryTree(): array
    {
        $parents = Category::with('children')->whereNull('parent_id')->get();

        $tree = [];
        foreach ($parents as $parent) {
            $tree[] = [
                'id' => $parent->id,
                'name' => $parent->name,
                'children' => $parent->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                    ];
                })->toArray(),
            ];
        }

        return $tree;
    }
}
