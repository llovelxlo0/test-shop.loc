<?php
namespace App\Services;

use App\Models\Goods;
use App\Models\ViewHistory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Collection;

class ViewHistoryService
{
    // Ключ сессии для хранения истории просмотров
    const SESSION_KEY = 'view_goods_history';
    // Максимальное количество записей в истории просмотров
    const LIMIT = 10;

    // Добавление товара в историю просмотров
    public function add(Goods $goods):  void
    {
        $history = session()->get(self::SESSION_KEY, []);

        // Удаляем товар, если он уже есть в истории
        $history = array_values(array_diff($history, [$goods->id]));

        // Добавляем товар в начало истории
        array_unshift($history, $goods->id);

        // Ограничиваем размер истории
        $history = array_slice($history, 0, self::LIMIT);
        session()->put(self::SESSION_KEY, $history);
    }
    // Получение истории просмотров
    public function get() : Collection
    {
        $ids = session()->get(self::SESSION_KEY, []);
        if (empty($ids)){
            return collect();
        }
        $goods = Goods::whereIn('id', $ids)->get()->keyBy('id');
        // Сортируем товары в соответствии с порядком в истории просмотров
        return collect($ids)->map(fn($id) => $goods[$id] ?? null)->filter();
    }
}