<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\CartService;
use App\Models\Order;

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
            return redirect()->route('cart.view')->with('success', 'Заказ успешно создан');
        } catch (\Exception $e) {
            return redirect()->route('cart.view')->with('error', $e->getMessage());
        }
    }
    public function confirmation(Order $order)
    {
    return view('order.view', compact('order'));
    }
}
