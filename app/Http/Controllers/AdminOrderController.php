<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index()
    {
        $this->authorize('viewAll', Order::class);
        $orders = $orders = Order::query()
            ->with(['user:id,name', 'items.goods:id,name,image'])
            ->latest()
            ->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }
}
