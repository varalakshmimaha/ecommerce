<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();

        if (!$user->is_admin) {
            $walletBalance    = WalletTransaction::balanceFor($user->id);
            $lifetimeEarnings = (float) Commission::where('beneficiary_user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->sum('amount');
            $totalOrders      = Order::where('user_id', $user->id)->count();
            $recentOrders     = Order::where('user_id', $user->id)
                ->latest()->take(5)
                ->get(['id', 'order_number', 'total_amount', 'wallet_used', 'payment_method', 'order_status', 'payment_status', 'created_at']);

            return view('admin.dashboard-user', compact(
                'user', 'walletBalance', 'lifetimeEarnings', 'totalOrders', 'recentOrders'
            ));
        }

        $stats = [
            'total_orders'       => Order::count(),
            'pending_orders'     => Order::where('order_status', 'pending')->count(),
            'delivered_orders'   => Order::where('order_status', 'delivered')->count(),
            'total_revenue'      => (float) Order::where('payment_status', 'verified')->sum('total_amount'),
            'total_products'     => Product::count(),
            'total_customers'    => User::where('role', 'customer')->count(),
            'total_affiliates'   => User::where('role', 'affiliate')->count(),
            'total_rms'          => User::where('role', 'rm')->count(),
            'total_managers'     => User::where('role', 'manager')->count(),
            'total_wallet'       => (float) WalletTransaction::where('type', 'credit')->where('status', 'approved')->sum('amount')
                                  - (float) WalletTransaction::where('type', 'debit')->where('status', 'approved')->sum('amount'),
        ];

        $recentOrders = Order::latest()->take(8)->get(['id', 'order_number', 'name', 'mobile', 'total_amount', 'order_status', 'payment_status', 'created_at']);

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}

