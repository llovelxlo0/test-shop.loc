<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Order::class);
        $orders = $orders = Order::query()
            ->with(['user:id,name', 'items.goods:id,name,image'])
            ->latest()
            ->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.goods', 'user']);
        return view('admin.orders.show', compact('order'));
    }
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $request->validate([
            'status' => 'required|string',
        ]);
        $order->update([
            'status' => $request->status,
        ]);
        return back()->with('success', 'Статус заказа обновлен.');
    }
}
