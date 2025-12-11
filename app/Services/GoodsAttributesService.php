<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\Attribute;

class GoodsAttributesService
{
    public function syncAttributes(Goods $good, array $attributesData): void
    {
        // Удаляем все старые связи
        $good->attributes()->detach();

        if (empty($attributesData)) {
            return;
        }

        foreach ($attributesData as $key => $attrData) {
            $name  = $attrData['name']  ?? null;
            $value = $attrData['value'] ?? null;

            // Если и имя, и значение пустые — пропускаем
            if (empty($name) && empty($value)) {
                continue;
            }

            // 1) Фиксированные атрибуты (ключ — ID из таблицы attributes)
            if (is_numeric($key)) {
                if (!empty($value)) {
                    $good->attributes()->attach((int) $key, ['value' => $value]);
                }
                continue;
            }

            // 2) Кастомные атрибуты (имя + значение)
            if (!empty($name) && !empty($value)) {
                $attribute = Attribute::firstOrCreate(['name' => $name]);
                $good->attributes()->attach($attribute->id, ['value' => $value]);
            }
        }
    }
}
