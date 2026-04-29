<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Commission::with(['order:id,order_number,total_amount,order_status,created_at'])
            ->forUser($userId);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $perPage = (int) $request->query('per_page', 20);
        $items   = $query->orderByDesc('created_at')->paginate(min(max($perPage, 1), 50));

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function summary(Request $request)
    {
        $userId = $request->user()->id;

        $totals = [
            'pending'  => (float) Commission::forUser($userId)->pending()->sum('amount'),
            'approved' => (float) Commission::forUser($userId)->approved()->sum('amount'),
            'paid'     => (float) Commission::forUser($userId)->paid()->sum('amount'),
        ];
        $totals['lifetime'] = round($totals['pending'] + $totals['approved'] + $totals['paid'], 2);

        $monthly = Commission::forUser($userId)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderByDesc('year')->orderByDesc('month')
            ->limit(12)->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'totals'  => $totals,
                'monthly' => $monthly,
            ],
        ]);
    }
}
