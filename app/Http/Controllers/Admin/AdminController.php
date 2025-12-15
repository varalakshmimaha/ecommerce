<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $stats = [
            'total_orders' => \App\Models\Order::count(),
            'pending_orders' => \App\Models\Order::where('order_status', 'pending')->count(),
            'total_products' => \App\Models\Product::count(),
            'total_revenue' => \App\Models\Order::where('payment_status', 'verified')->sum('total_amount'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

