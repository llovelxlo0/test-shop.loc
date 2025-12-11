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
        $query = Goods::query();

        $parentId      = $request->input('parent_id');       // родительская категория
        $subcategoryId = $request->input('subcategory_id');  // подкатегория
        $attributeFilters = $request->input('attributes', []); // фильтр по EAV

        /*
         * 1. Фильтр по категориям
         */
        if (!empty($parentId)) {
            $parentId = (int) $parentId;

            // Все подкатегории выбранного родителя
            $childIds = Category::where('parent_id', $parentId)
                ->pluck('id')
                ->toArray();

            // Товары из родителя + всех его подкатегорий
            $query->whereIn('category_id', array_merge([$parentId], $childIds));
        }

        if (!empty($subcategoryId)) {
            $subcategoryId = (int) $subcategoryId;
            $query->where('category_id', $subcategoryId);
        }

        /*
         * 2. Фильтр по EAV-атрибутам
         * Формат: attributes[attribute_id] = [value1, value2, ...]
         */
        if (!empty($attributeFilters)) {
            $query->where(function ($q) use ($attributeFilters) {
                foreach ($attributeFilters as $attrId => $values) {
                    $values = array_filter((array) $values);

                    // Если по этому атрибуту ничего не выбрали — пропускаем
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

        // Товары по всем фильтрам
        $goods = $query->get();

        /*
         * 3. Дерево категорий для выпадающего списка
         */
        $tree = $this->buildCategoryTree();

        /*
         * 4. Список категорий, которые сейчас участвуют в выборке
         * (чтобы не показывать в фильтре атрибуты, которых нет в конкретной категории)
         */
        $categoryIdsForFilter = Goods::query()
            ->when($parentId, function ($q) use ($parentId) {
                $childIds = Category::where('parent_id', $parentId)
                    ->pluck('id')
                    ->toArray();

                $q->whereIn('category_id', array_merge([$parentId], $childIds));
            })
            ->when($subcategoryId, function ($q) use ($subcategoryId) {
                $q->where('category_id', $subcategoryId);
            })
            ->distinct()
            ->pluck('category_id');

        /*
         * 5. Атрибуты, которые реально есть у товаров этих категорий
         */
        $attributes = Attribute::whereHas('goods', function ($q) use ($categoryIdsForFilter) {
                if ($categoryIdsForFilter->isNotEmpty()) {
                    $q->whereIn('goods.category_id', $categoryIdsForFilter);
                }
            })
            ->get();

        /*
         * 6. Для каждого атрибута — набор возможных значений (для checkboxes)
         */
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
        $parents = Category::whereNull('parent_id')->get();
        $tree = [];

        foreach ($parents as $parent) {
            $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
        }

        return $tree;
    }
}
