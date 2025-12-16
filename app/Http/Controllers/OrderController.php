<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\CartService;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;
    protected $cartService;

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
        return view('order.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.goods']);
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
    return view('order.view', compact('order'));
    }
}
