<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;

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
        return Goods::create([
            'name' => $data['name'],
            //'parent_id' => $data['parent_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'image' => $imagePath,
            'price' => $data['price'],
            'stock' => $data['stock'],
            'description' => $data['description'] ?? ''
        ]);
    }
    public function updateGoods(Goods $good, array $data, ?string $imagePath = null)
    {
        $good->name = $data['name'];
        $good->category_id = $data['category_id'] ?? null;
        if ($imagePath) {
            $good->image = $imagePath;
        }
        $good->price = $data['price'];
        $good->stock = $data['stock'];
        $good->description = $data['description'] ?? '';
        $good->save();

        return $good;
    }
    public function deleteGoods(Goods $good)
    {
        // Дополнительная логика проверки перед удалением, если необходимо
        return $good->delete();
    }
}
