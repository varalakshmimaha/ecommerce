<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order, CommissionService $commissions)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'tracking_id' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $previousStatus = $order->order_status;
        $order->update($validated);

        if ($validated['order_status'] === 'delivered' && $previousStatus !== 'delivered') {
            $commissions->approveForOrder($order);
        } elseif ($validated['order_status'] === 'cancelled' && $previousStatus !== 'cancelled') {
            $commissions->reverseForOrder($order);
        }

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function verifyPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:verified,rejected',
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);

        if ($validated['payment_status'] === 'verified' && $order->order_status === 'pending') {
            $order->update(['order_status' => 'confirmed']);
        }

        return redirect()->back()->with('success', 'Payment status updated successfully');
    }

    public function backfillCommissions(Order $order)
    {
        $added = app(CommissionService::class)->backfillForOrder($order);
        $msg = $added > 0
            ? "$added missing commission(s) created for order #{$order->order_number}."
            : "No missing commissions to add for order #{$order->order_number}.";
        return redirect()->back()->with('success', $msg);
    }

    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('invoices.order', compact('order'));
    }
}
