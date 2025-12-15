<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function track($orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $order]);
    }
}
