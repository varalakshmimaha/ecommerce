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
        $activeTab   = $request->get('tab', 'pending');

        // Base filter: role + month only (shared across both tabs for counts)
        $baseFiltered = $rows->filter(function ($r) use ($filterRole, $filterMonth) {
            if ($filterRole && $r->beneficiary_role !== $filterRole) return false;
            if ($filterMonth) {
                $ym = $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT);
                if ($ym !== $filterMonth) return false;
            }
            return true;
        });

        $pendingRows = $baseFiltered->filter(fn($r) => $r->month_status !== 'paid');
        $paidRows    = $baseFiltered->filter(fn($r) => $r->month_status === 'paid');

        $filtered = $activeTab === 'paid' ? $paidRows : $pendingRows;

        return view('admin.paid-earnings.index', [
            'rows'         => $filtered,
            'userNames'    => $userNames,
            'filterRole'   => $filterRole,
            'filterMonth'  => $filterMonth,
            'totalPaid'    => $rows->where('month_status', 'paid')->sum('total_amount'),
            'totalPending' => $rows->where('month_status', '!=', 'paid')->sum('total_amount'),
            'paidCount'    => $paidRows->count(),
            'pendingCount' => $pendingRows->count(),
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

        return redirect()->route('admin.paid-earnings.index', ['tab' => 'pending'])->with('success', 'Earnings marked as paid.');
    }
}
