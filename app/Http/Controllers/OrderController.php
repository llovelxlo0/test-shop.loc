<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\CartService;

class OrderController extends Controller
{
    protected $orderService;
    protected $cartService;

    public function __construct(OrderService $orderService , CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }
    public function checkout(Request $request)
    {
        try {
            $order = $this->orderService->checkout();
            return redirect()->route('orders.view', ['order' => $order->id])->with('success', 'Заказ успешно создан');
        } catch (\Exception $e) {
            return redirect()->route('cart.view')->with('error', $e->getMessage());
        }
    }
}
