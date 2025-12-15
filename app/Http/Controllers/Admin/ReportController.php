<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.reports.overall-sales');
    }

    public function productSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = OrderItem::with(['product', 'order'])
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'verified');
            });

        if ($request->has('download')) {
            $data = $query->get()->map(function ($item) {
                return [
                    'Product Name' => $item->product_name,
                    'Quantity' => $item->quantity,
                    'Price' => '₹' . number_format($item->price, 2),
                    'Subtotal' => '₹' . number_format($item->subtotal, 2),
                    'Order Date' => $item->order->created_at->format('Y-m-d H:i:s'),
                    'Order Number' => $item->order->order_number,
                ];
            });

            return Excel::download(new \App\Exports\GenericExport($data, 'Product Sales Report'), 'product-sales-report.xlsx');
        }

        $items = $query->paginate(50);
        return view('admin.reports.product-sales', compact('items', 'startDate', 'endDate'));
    }

    public function orderSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'verified');

        if ($request->has('download')) {
            $data = $query->get()->map(function ($order) {
                return [
                    'Order Number' => $order->order_number,
                    'Customer Name' => $order->name,
                    'Mobile' => $order->mobile,
                    'Email' => $order->email ?? 'N/A',
                    'Subtotal' => '₹' . number_format($order->subtotal, 2),
                    'GST' => '₹' . number_format($order->gst_amount, 2),
                    'Shipping' => '₹' . number_format($order->shipping_charge, 2),
                    'Total' => '₹' . number_format($order->total_amount, 2),
                    'Order Status' => ucfirst($order->order_status),
                    'Date' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return Excel::download(new \App\Exports\GenericExport($data, 'Order Sales Report'), 'order-sales-report.xlsx');
        }

        $orders = $query->paginate(50);
        return view('admin.reports.order-sales', compact('orders', 'startDate', 'endDate'));
    }

    public function overallSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $stats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'verified')
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(subtotal) as total_subtotal'),
                DB::raw('SUM(gst_amount) as total_gst'),
                DB::raw('SUM(shipping_charge) as total_shipping')
            )
            ->first();

        // Return JSON if requested
        if ($request->has('json')) {
            return response()->json([
                'stats' => $stats,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }

        if ($request->has('download')) {
            $data = [
                [
                    'Metric' => 'Total Orders',
                    'Value' => $stats->total_orders,
                ],
                [
                    'Metric' => 'Total Revenue',
                    'Value' => '₹' . number_format($stats->total_revenue, 2),
                ],
                [
                    'Metric' => 'Total Subtotal',
                    'Value' => '₹' . number_format($stats->total_subtotal, 2),
                ],
                [
                    'Metric' => 'Total GST',
                    'Value' => '₹' . number_format($stats->total_gst, 2),
                ],
                [
                    'Metric' => 'Total Shipping',
                    'Value' => '₹' . number_format($stats->total_shipping, 2),
                ],
            ];

            return Excel::download(new \App\Exports\GenericExport($data, 'Overall Sales Report'), 'overall-sales-report.xlsx');
        }

        return view('admin.reports.overall-sales', compact('stats', 'startDate', 'endDate'));
    }
}

