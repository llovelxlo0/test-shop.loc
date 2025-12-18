<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

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
}
