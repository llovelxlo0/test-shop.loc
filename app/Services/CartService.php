<?php
namespace App\Services;
use App\Models\CartItem;
use App\Models\Goods;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Exception;


class CartService
{
    public function addToCart(int $goodsId, int $quantity)
    {
        // Логика добавления товара в корзину
        $product = Goods::findOrFail($goodsId);

        // Проверка наличия товара на складе
        if ($product->stock < $quantity) {
            throw new \Exception('Недостаточно товара на складе');
        }
        if (Auth::check()) {
            $userId = Auth::id();
            //дб корзина пользователя
            $existing = CartItem::where('user_id', $userId)
                ->where('goods_id', $goodsId)
                ->first();
            if ($existing) {
                // Обновляем количество, если товар уже в корзине но не привышаем лимит
                $newQty = min($existing->quantity + $quantity, $product->stock);
                $existing->quantity = $newQty;
                $existing->save();
            } 
            else {
            // Создаем новую запись в корзине
            CartItem::create([
                'user_id' => $userId,
                'goods_id' => $goodsId,
                'quantity' => $quantity,
                'price' => $product->price,
                ]);
            }
        } else {
            // Логика для неавторизованных пользователей (сессия)
            $cart = session()->get('cart', []);
            
            if (isset($cart[$goodsId])) {
                // Обновляем количество, если товар уже в корзине но не привышаем лимит
                $newQty = min($cart[$goodsId]['quantity'] + $quantity, $product->stock);
                $cart[$goodsId]['quantity'] = $newQty;
            } else {
                // Добавляем новый товар в корзину
                $cart[$goodsId] = [
                    'goods_id' => $goodsId,
                    'quantity' => min($quantity, $product->stock),
                    'price' => $product->price,
                ];
            }
            session()->put('cart', $cart);
        }
    }

    public function updateCartItem (int $goodsId, int $newQty)
    {
        // Логика обновления количества товара в корзине
        $product = Goods::findOrFail($goodsId);

        if ($newQty < 1) {
            throw new \Exception('Количество должно быть не меньше 1');
        }

        if ($product->stock < $newQty) {
            throw new \Exception('Недостаточно товара на складе');
        }

        if (Auth::check()) {
            $userId = Auth::id();
            $item = CartItem::where('user_id', $userId)
                ->where('goods_id', $goodsId)
                ->firstOrFail();
            $item->quantity = $newQty;
            $item->save();
        } else {
            $cart = session()->get('cart', []);
            if (!isset($cart[$goodsId])) {
                throw new \Exception('Товар не найден в корзине');
            }
            $cart[$goodsId]['quantity'] = $newQty;
            session()->put('cart', $cart);
        }
    }

    public function removeFromCart(int $goodsId) 
    {
        // Логика удаления товара из корзины
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                ->where('goods_id', $goodsId)
                ->delete();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$goodsId])) {
                unset($cart[$goodsId]);
                session()->put('cart', $cart);
            }
        }
    }
    public function getCartContents()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())->get()->map(function($ci) {
                return [
                    'goods' => $ci->goods,
                    'quantity' => $ci->quantity,
                    'price' => $ci->price,
                ];
            });
        } else {
            $sessionCart = session()->get('cart', []);
            return collect($sessionCart)->map(function($items, $goodsId) {
                $product = Goods::find($goodsId);
                if (!$product) return null;
                $quantity = $items['quantity'] ?? 0;
                return [
                    'goods' => $product,
                    'quantity' => min($quantity, $product->stock),
                    'price' => $product->price,
                ];
            })->filter(); // Убираем null элементы
        }
    }
    // При авторизации пользователя, переносим корзину из сессии в базу данных
    public function mergeSessionCartToUser (int $userId)
    {
        $sessionCart = session()->get('cart', []);
        foreach ($sessionCart as $goodsId => $items) {
            $quantity = is_array($items) ? $items['quantity'] : $items;  // Поддержка обоих форматов
            $this->addToCartForUser($userId, $goodsId, $quantity);
        }
        session()->forget('cart');
    }

    protected function addToCartForUser(int $userId, int $goodsId, int $quantity)
    {
        $product = Goods::findOrFail($goodsId);
        if ($product->stock < $quantity) {
            $quantity = $product->stock; // Ограничиваем количеством на складе
        }
        $existing = CartItem::where('user_id', $userId)
            ->where('goods_id', $goodsId)
            ->first();
        if ($existing) {
            // Обновляем количество, если товар уже в корзине но не привышаем лимит
            $existing->quantity = min($existing->quantity + $quantity, $product->stock);
            $existing->save();
        } else {
            // Создаем новую запись в корзине
            CartItem::create([
                'user_id' => $userId,
                'goods_id' => $goodsId,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }
    }

    protected function getCartFromSession()
{
    $sessionCart = session()->get('cart', []);
    $items = [];

    foreach ($sessionCart as $goodsId => $row) {
        $qty = is_array($row) ? ($row['quantity'] ?? 0) : (int)$row;

        $product = Goods::find($goodsId);
        if (!$product) {
            continue;
        }

        $items[] = [
            'goods'     => $product,
            'quantity'  => min($qty, $product->stock),
            'price'     => $product->price,
        ];
    }

    return collect($items);
}

    protected function getCartFromDatabase(int $userId)
    {
        // Получаем товары из базы данных для авторизованного пользователя
        $dbItems = CartItem::where('user_id', $userId)->get();
        $items = [];
        // Перебираем элементы и получаем связанные товары
        foreach ($dbItems as $dbItem) {
            $product = $dbItem->goods;
            if ($product) {
                $items[]= [
                    'goods' => $product,
                    'quantity' => min($dbItem->quantity, $product->stock),
                    'price' => $dbItem->price,
                ];
            }
        }
        return $items;
    }
}