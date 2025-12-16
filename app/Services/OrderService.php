<?php
namespace App\Services;

use App\Models\CartItem;
use App\Models\Goods;
use App\Services\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Policies\OrderPolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class OrderService
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function checkout(array $checkoutData = []): Order
{
    $user = Auth::user(); // может быть null
    $items = $this->cartService->getCartContents();

    if ($items->isEmpty()) {
        throw new \Exception('Корзина пуста');
    }

    return DB::transaction(function () use ($user, $items, $checkoutData) {

        $order = Order::create([
            'user_id' => $user?->id,
            'status'  => Order::STATUS_PENDING,
            'total'   => '0.00',
            'recipient_name' => $checkoutData['recipient_name'] ?? null,
            'phone' => $checkoutData['phone'] ?? null,
            'address' => $checkoutData['address'] ?? null,
            'comment' => $checkoutData['comment'] ?? null,
            'currency' =>$checkoutData['currency'] ?? 'UAH',
        ]);

        $total = '0.00';

        foreach ($items as $item) {
            /** @var Goods $product */
            $product = $item['goods'];
            $qty = (int) $item['quantity'];
            if ($qty < 1) {
                throw new \Exception('Некорректное количество товара в корзине');
            }

            // блокируем товар (если есть stock)
            $locked = Goods::where('id', $product->id)->lockForUpdate()->first();
            if (!$locked) {
                throw new \Exception("Товара c ID {$product->id} не существует");
            }

            if ($locked->stock < $qty) {
                throw new \Exception("Товара {$locked->name} недостаточно на складе");
            }

            $locked->decrement('stock', $qty);

            // цена как строка (decimal)
            $price = (string) $locked->price;
            $line  = bcmul($price, (string)$qty, 2);
            $total = bcadd($total, $line, 2);

            OrderItem::create([
                'order_id' => $order->id,
                'goods_id' => $locked->id,
                'quantity' => $qty,
                'price'    => $locked->price,
                'subtotal' => $line,
            ]);
        }

        $order->update(['total' => $total]);

        // очищаем корзину
        if ($user) {
            CartItem::where('user_id', $user->id)->delete();
        } else {
            session()->forget('cart');
        }

        return $order;
    });
}
}