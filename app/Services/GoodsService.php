<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\Attribute;

class GoodsService
{
    public function getParentCategories()
    {
        return Category::whereNull('parent_id')->pluck('name', 'id');
    }

    public function getChildCategories($parentId)
    {
        return Category::where('parent_id', $parentId)->pluck('name', 'id');
    }
    public function createGoods(array $data, ?string $imagePath = null)
    {
            $good = Goods::create([
            'name' => $data['name'],
            'category_id' => $data['category_id'] ?? null,
            'image' => $imagePath,
            'price' => $data['price'],
            'stock' => $data['stock'],
            'description' => $data['description'] ?? ''
        ]);

        //EAV
        if (!empty($data['attributes'])) {
            foreach ($data['attributes'] as $key => $attrData) {
                if (!empty($attrData['value'])) {
                    if (!empty($attrData['name'])) {
                    $attribute = Attribute::firstOrCreate(['name' => $attrData['name']]);
                    $good->attributes()->attach($attribute->id, ['value' => $attrData['value']]);
                    }
                elseif (is_numeric($key)) {
                    $good->attributes()->attach($key, ['value' => $attrData['value']]);
                    }
                }
            }
        }
        return $good;
    }
    public function updateGoods(Goods $good, array $data, ?string $imagePath = null)
{
    // Обновляем обычные поля товара
    $good->update([
        'name'        => $data['name'],
        'category_id' => $data['category_id'] ?? null,
        'image'       => $imagePath ?? $good->image,
        'price'       => $data['price'],
        'stock'       => $data['stock'],
        'description' => $data['description'] ?? '',
    ]);

    // EAV
    $good->attributes()->detach(); // очищаем все старые связи

    if (!empty($data['attributes'])) {
        foreach ($data['attributes'] as $key => $attrData) {

            if (empty($attrData['value']) && empty($attrData['name'])) {
                continue;
            }

            if (is_numeric($key)) {
                if (!empty($attrData['value'])) {
                    $good->attributes()->attach($key, ['value' => $attrData['value']]);
                }
                continue;
            }

            if (!empty($attrData['name']) && !empty($attrData['value'])) {
                $attribute = Attribute::firstOrCreate(['name' => $attrData['name']]);
                $good->attributes()->attach($attribute->id, ['value' => $attrData['value']]);
            }
        }
    }
    return $good;
}
    public function deleteGoods(Goods $good)
    {
        return $good->delete();
    }
}
