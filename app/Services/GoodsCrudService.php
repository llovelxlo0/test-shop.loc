<?php

namespace App\Services;

use App\Models\Goods;

class GoodsCrudService
{
    public function create(array $data, ?string $imagePath = null): Goods
    {
        return Goods::create([
            'name'        => $data['name'],
            'category_id' => $data['category_id'] ?? null,
            'image'       => $imagePath,
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'description' => $data['description'] ?? '',
        ]);
    }

    public function update(Goods $good, array $data, ?string $imagePath = null): Goods
    {
        $good->update([
            'name'        => $data['name'],
            'category_id' => $data['category_id'] ?? null,
            'image'       => $imagePath ?? $good->image,
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'description' => $data['description'] ?? '',
        ]);

        return $good;
    }

    public function delete(Goods $good): bool
    {
        return $good->delete();
    }
}
