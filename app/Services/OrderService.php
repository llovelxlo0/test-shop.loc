<?php
namespace App\Services;

use App\Models\CartItem;
use App\Models\Goods;
use App\Services\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class OrderService
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function checkout()
    {
        if (!Auth::check()) {
            throw new \Exception('Пользователь не авторизован');
        }

        $userId = Auth::id();
        $items = $this->cartService->getCartContents();
        if ($items->isEmpty()) {
            throw new \Exception('Корзина пуста');
        }
        return DB::transaction(function() use ($userId, $items){
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'new',
                'total' => 0
            ]);
            $total = 0;
            foreach ($items as $item) {
                $product = $item['goods'];
                $lockedProduct = Goods::where('id', $product->id)
                                ->lockForUpdate()
                                ->first();
                if (!$lockedProduct) {
                    throw new \Exception("Товара c ID {$product->id} не существует");
                }

                if ($lockedProduct -> stock < $item['quantity']){
                    throw new \Exception("Товара {$product->name} недостаточно на складе");
                }

                $lockedProduct->decrement('stock', $item['quantity']);
            
                OrderItem::create([
                    'order_id' => $order->id,
                    'goods_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $lockedProduct->price
                ]);
                $total += $lockedProduct->price * $item['quantity'];
            }

            // Обновляем общую сумму заказа
            $order->update(['total' => $total]);

            // Очищаем корзину
            if (Auth::check()){
                    CartItem::where('user_id', Auth::id())->delete();
            } else {
            session()->forget('cart');
            }
            return $order;
        });
    }
}