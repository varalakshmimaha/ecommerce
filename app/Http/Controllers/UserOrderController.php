<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with('items')
            ->get();
        return view('frontend.dashboard.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with('items.product.images', 'addresses')
            ->findOrFail($id);

        return view('user.orders.show', compact('order'));
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Add logic to check if the order can be cancelled
        if ($order->order_status !== 'pending' && $order->order_status !== 'processing') {
            return redirect()->route('user.orders.show', $order->id)->with('error', 'Order cannot be cancelled at this stage.');
        }

        $request->validate([
            'cancellation_remark' => 'required|string|max:255',
        ]);

        $order->order_status = 'cancelled';
        $order->cancellation_remark = $request->cancellation_remark;
        $order->save();

        return redirect()->route('user.orders.show', $order->id)->with('success', 'Order has been cancelled.');
    }
}
