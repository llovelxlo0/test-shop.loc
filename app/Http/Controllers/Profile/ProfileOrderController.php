<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ProfileOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with(['items.goods:id,name,image'])
            ->latest()
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.goods', 'user']);
        return view('orders.show', compact('order'));
    }
}
