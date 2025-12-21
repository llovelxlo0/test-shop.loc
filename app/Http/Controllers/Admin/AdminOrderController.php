<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusChanged;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);
        $orders = Order::with('user')
        ->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->when($request->filled('user'), function ($q) use ($request) {
            $q->whereHas('user', function ($u) use ($request) {
                $u->where('name', 'like', '%' . $request->user . '%')
                  ->orWhere('id', $request->user);
            });
        })
        ->when($request->filled('from'), fn ($q) =>
            $q->whereDate('created_at', '>=', $request->from)
        )
        ->when($request->filled('to'), fn ($q) =>
            $q->whereDate('created_at', '<=', $request->to)
        )
        ->latest()
        ->paginate(20)
        ->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('updateStatus', $order);
        $request->validate([
        'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
    ]);

    if ($order->status === $request->status) {
        return back();
    }
    $oldStatus = $order->status;

    OrderStatusLog::create([
        'order_id'   => $order->id,
        'user_id'    => auth()->id(),
        'old_status' => $oldStatus,
        'new_status' => $request->status,
    ]);

    $order->update([
        'status' => $request->status,
    ]);

    if ($order->user) {
        $order->user->notify(
            new OrderStatusChanged($order, $oldStatus, $request->status)
        );
    }
    return back()->with('success', 'Статус заказа обновлен.');
    }
}
