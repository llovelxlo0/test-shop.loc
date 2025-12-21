<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\Category;
use Illuminate\Support\Collection;

class RelatedProductService
{
    public function getRelatedProducts(Goods $goods, int $limit = 6)
    {
        //подгрузка всего
        $goods->loadMissing(['attributes', 'category.attributes']);

        $category = $goods->category;

        if(!$category) {
            // Если у товара нет категории, возвращаем пустую коллекцию
            return collect();
        }

        // Получаем сравнимые атрибуты категории
        $comparableAttributes = $this->getComparableAttributesForCategory($category);

        // Ищем связанные товары по сравнимым атрибутам
        $relatedGoods = $this->findByAttributes($goods, $comparableAttributes, $limit);

        if ($relatedGoods->isEmpty()) {
            // Если не найдено связанных товаров, ищем по категории
            $relatedGoods = $this->findByCategoryFallback($goods, $limit);
        }
        return $relatedGoods;
    }
    //Возвращаем аттрибуты по которым ищем похожие товары
    protected function getComparableAttributesForCategory(Category $category) : Collection
    {
        return $category->attributes()->get()->filter(fn($attr) => $attr->pivot->is_comparable ?? true);
    }

    // Поиск похожих товаров по совпадаюзим EAV-аттрибутам
    protected function findByAttributes(Goods $goods, Collection $comparableAttributes, int $limit) : Collection
    {
        if ($comparableAttributes->isEmpty()) {
            return collect();
        }
        $goodsAttributes = $goods->attributes()->get();

        //запрос на похожие товары
        $query = Goods::query()->where('id', '!=', $goods->id);

        $query->where(function($q) use ($comparableAttributes, $goodsAttributes) {
            foreach ($comparableAttributes as $attr) {
                //значение атрибута товара
                $value = $goodsAttributes->firstWhere('id', $attr->id)?->pivot->value;
                if (!$value) {
                    continue;
                }
                //добавляем условие поиска
                $q->orWhereHas('attributes', function($qa) use ($attr, $value) {
                    $qa->where('attributes.id', $attr->id)
                              ->where('value', $value);
                });
            }
        });
        return $query->take($limit)->get();
    }
    // Поиск похожих товаров по категории (запасной вариант)
    protected function findByCategoryFallback(Goods $goods, int $limit) : Collection
    {
        if (!$goods->category_id) {
            return collect();
        }
        return Goods::where('category_id', $goods->category_id)
            ->where('id', '!=', $goods->id)
            ->take($limit)
            ->get();
    }
}
