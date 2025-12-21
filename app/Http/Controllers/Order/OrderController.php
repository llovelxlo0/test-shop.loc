<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;


class OrderController extends Controller
{
    protected OrderService $orderService;
    protected CartService $cartService;

    public function __construct(OrderService $orderService , CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }
    public function index()
    {
        $this->authorize('viewAny', Order::class);
        $user = auth()->user();
        $orderQuery = Order::query()->with('items');
        if (! $user->isAdmin()) {
            $orderQuery->where('user_id', $user->id);
        }
        $orders = $orderQuery->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.goods', 'user', 'statusLogs.user']);
        return view('orders.show', compact('order'));
    }

    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);
        $cartItems = $request->user()->cartItems()->with('good')->get();
        if ($cartItems->isEmpty()) {
            return back()->withErrors(['cart' => 'Корзина пустая.']);
        }
        $total = 0;
        foreach ($cartItems as $item) {
            $total += (int) $item->goods->price * (int) $item->quantity;
        }
        return $this->checkout($request, $this->orderService);
    }
    public function checkout(StoreOrderRequest $request, OrderService $orderService)
    {
        try {
            $data = $request->validated();
            $order = $orderService->checkout($data);
            return redirect()->route('cart.view', $order)->with('success', 'Заказ оформлен.');
        } catch (\Exception $e) {
            return redirect()->route('cart.view')->with('error', $e->getMessage());
        }
    }
    public function confirmation(Order $order)
    {
    return view('orders.show', compact('order'));
    }
}
