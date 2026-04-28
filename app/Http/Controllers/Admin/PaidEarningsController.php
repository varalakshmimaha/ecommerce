<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaidEarningsController extends Controller
{
    public function index(Request $request)
    {
        // Monthly commission totals per user (affiliate/rm/manager), excludes reversed
        $rows = Commission::whereNotIn('status', ['reversed'])
            ->whereIn('beneficiary_role', ['affiliate', 'rm', 'manager'])
            ->selectRaw("
                beneficiary_user_id,
                beneficiary_role,
                YEAR(created_at)  AS year,
                MONTH(created_at) AS month,
                SUM(amount)       AS total_amount,
                COUNT(*)          AS commission_count,
                CASE
                    WHEN SUM(CASE WHEN status = 'paid'     THEN 1 ELSE 0 END) = COUNT(*) THEN 'paid'
                    WHEN SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) > 0        THEN 'pending'
                    ELSE 'approved'
                END AS month_status
            ")
            ->groupByRaw('beneficiary_user_id, beneficiary_role, YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC, beneficiary_role')
            ->get();

        // Load user names
        $userIds   = $rows->pluck('beneficiary_user_id')->unique()->values()->all();
        $userNames = User::whereIn('id', $userIds)->pluck('name', 'id');

        // Apply filters
        $filterRole  = $request->get('role');
        $filterMonth = $request->get('month'); // format: YYYY-MM
        $filterStatus = $request->get('status');

        $filtered = $rows->filter(function ($r) use ($filterRole, $filterMonth, $filterStatus, $userNames) {
            if ($filterRole && $r->beneficiary_role !== $filterRole) return false;
            if ($filterMonth) {
                $ym = $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT);
                if ($ym !== $filterMonth) return false;
            }
            if ($filterStatus && $r->month_status !== $filterStatus) return false;
            return true;
        });

        return view('admin.paid-earnings.index', [
            'rows'        => $filtered,
            'userNames'   => $userNames,
            'filterRole'  => $filterRole,
            'filterMonth' => $filterMonth,
            'filterStatus'=> $filterStatus,
            'totalPaid'   => $rows->where('month_status', 'paid')->sum('total_amount'),
            'totalPending'=> $rows->where('month_status', '!=', 'paid')->sum('total_amount'),
        ]);
    }

    public function markPaid(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year'    => 'required|integer',
            'month'   => 'required|integer|min:1|max:12',
        ]);

        Commission::where('beneficiary_user_id', $request->user_id)
            ->whereNotIn('status', ['reversed', 'paid'])
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month)
            ->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Earnings marked as paid.');
    }
}
