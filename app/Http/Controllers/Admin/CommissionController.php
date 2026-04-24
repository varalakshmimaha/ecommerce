<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GenericExport;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Commission::with(['beneficiary:id,name,mobile,role', 'order:id,order_number,total_amount']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('role')) {
            $query->where('beneficiary_role', $request->role);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('beneficiary', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%");
            });
        }

        $commissions = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        $totals = [
            'pending' => (float) Commission::pending()->sum('amount'),
            'approved' => (float) Commission::approved()->sum('amount'),
            'paid' => (float) Commission::paid()->sum('amount'),
        ];

        $roleRows = Commission::select('beneficiary_role', 'status', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('beneficiary_role', 'status')
            ->get();

        $roleTotals = [];
        foreach (['manager', 'rm', 'affiliate'] as $r) {
            $roleTotals[$r] = ['total' => 0.0, 'pending' => 0.0, 'approved' => 0.0, 'paid' => 0.0, 'count' => 0];
        }
        foreach ($roleRows as $row) {
            if (!isset($roleTotals[$row->beneficiary_role])) continue;
            if (in_array($row->status, ['pending', 'approved', 'paid'], true)) {
                $roleTotals[$row->beneficiary_role][$row->status] = (float) $row->total;
                $roleTotals[$row->beneficiary_role]['total'] += (float) $row->total;
                $roleTotals[$row->beneficiary_role]['count'] += (int) $row->cnt;
            }
        }

        return view('admin.commissions.index', compact('commissions', 'totals', 'roleTotals'));
    }

    public function markPaid(Request $request, Commission $commission)
    {
        $validated = $request->validate([
            'payout_ref' => 'nullable|string|max:255',
        ]);

        if ($commission->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved commissions can be marked paid.');
        }

        $commission->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payout_ref' => $validated['payout_ref'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Commission marked as paid.');
    }

    public function approve(Commission $commission)
    {
        if ($commission->status === 'pending') {
            $commission->update(['status' => 'approved']);
            return redirect()->back()->with('success', 'Commission approved.');
        }
        return redirect()->back()->with('error', 'Only pending commissions can be approved.');
    }

    public function reverse(Commission $commission)
    {
        if (in_array($commission->status, ['pending', 'approved'], true)) {
            $commission->update(['status' => 'reversed']);
            return redirect()->back()->with('success', 'Commission reversed.');
        }
        return redirect()->back()->with('error', 'Paid commissions cannot be reversed.');
    }

    public function export(Request $request)
    {
        $query = Commission::with(['beneficiary:id,name,mobile,role', 'order:id,order_number']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('role')) {
            $query->where('beneficiary_role', $request->role);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('beneficiary', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%");
            });
        }

        $rows = $query->orderByDesc('created_at')->get()->map(function ($c) {
            return [
                'Order'        => optional($c->order)->order_number ?? '-',
                'Beneficiary'  => optional($c->beneficiary)->name ?? 'User #'.$c->beneficiary_user_id,
                'Mobile'       => optional($c->beneficiary)->mobile ?? '',
                'Role'         => strtoupper($c->beneficiary_role),
                'Base Amount'  => (float) $c->base_amount,
                'Rate (%)'     => (float) $c->percentage,
                'Commission'   => (float) $c->amount,
                'Status'       => ucfirst($c->status),
                'Paid At'      => optional($c->paid_at)?->format('d M Y'),
                'Payout Ref'   => $c->payout_ref ?? '',
                'Created'      => $c->created_at?->format('d M Y H:i'),
            ];
        })->values()->all();

        $filename = 'commissions_' . now()->format('Ymd_His');
        if ($request->filled('role')) $filename .= '_' . $request->role;
        if ($request->filled('status')) $filename .= '_' . $request->status;

        return Excel::download(new GenericExport($rows, 'Commissions'), $filename . '.xlsx');
    }
}
