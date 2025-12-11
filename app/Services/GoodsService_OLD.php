<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\Attribute;

class GoodsService_OLD
{
    public function getParentCategories()
    {
        return Category::whereNull('parent_id')->pluck('name', 'id');
    }

    public function getChildCategories($parentId)
    {
        return Category::where('parent_id', $parentId)->pluck('name', 'id');
    }
    public function createGoods(array $data, ?string $imagePath = null): Goods
    {
    $good = Goods::create([
        'name'        => $data['name'],
        'category_id' => $data['category_id'] ?? null,
        'image'       => $imagePath,
        'price'       => $data['price'],
        'stock'       => $data['stock'],
        'description' => $data['description'] ?? '',
    ]);

    // EAV
    if (!empty($data['attributes']) && is_array($data['attributes'])) {
        foreach ($data['attributes'] as $key => $attrData) {

            $name  = trim($attrData['name']  ?? '');
            $value = trim($attrData['value'] ?? '');

            if ($name === '' && $value === '') {
                continue;
            }

            // СЛУЧАЙ 1: фиксированный атрибут (id в ключе массива, в форме name не указываем)
            if (is_numeric($key)) {
                if ($value !== '') {
                    $good->attributes()->attach((int)$key, ['value' => $value]);
                }
                continue;
            }

            // СЛУЧАЙ 2: новый кастомный атрибут по имени
            if ($name !== '' && $value !== '') {
                $attribute = Attribute::firstOrCreate(['name' => $name]);
                $good->attributes()->attach($attribute->id, ['value' => $value]);
            }
        }
    }

    return $good;
}

    public function updateGoods(Goods $good, array $data, ?string $imagePath = null): Goods
{
    // Обновляем обычные поля
    $good->update([
        'name'        => $data['name'],
        'category_id' => $data['category_id'] ?? null,
        'image'       => $imagePath ?? $good->image,
        'price'       => $data['price'],
        'stock'       => $data['stock'],
        'description' => $data['description'] ?? '',
    ]);

    // EAV: сначала чистим старые связи
    $good->attributes()->detach();

    if (!empty($data['attributes']) && is_array($data['attributes'])) {
        foreach ($data['attributes'] as $key => $attrData) {

            $name  = trim($attrData['name']  ?? '');
            $value = trim($attrData['value'] ?? '');

            if ($name === '' && $value === '') {
                continue;
            }

            if (is_numeric($key)) {
                if ($value !== '') {
                    $good->attributes()->attach((int)$key, ['value' => $value]);
                }
                continue;
            }

            if ($name !== '' && $value !== '') {
                $attribute = Attribute::firstOrCreate(['name' => $name]);
                $good->attributes()->attach($attribute->id, ['value' => $value]);
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
