<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use App\Models\Commission;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        // Past withdrawals with filters
        $query = Withdrawal::with(['user:id,name,mobile,role', 'creator:id,name']);
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%"));
        }
        $withdrawals = $query->orderByDesc('paid_at')->paginate(25)->withQueryString();

        // Outstanding balances (users with non-zero approved balance)
        $balances = Commission::with('beneficiary:id,name,mobile,role')
            ->where('status', 'approved')
            ->selectRaw('beneficiary_user_id, beneficiary_role, SUM(amount) as approved_total, COUNT(*) as rows_cnt')
            ->groupBy('beneficiary_user_id', 'beneficiary_role')
            ->orderByDesc('approved_total')
            ->get();

        $summary = [
            'outstanding' => (float) $balances->sum('approved_total'),
            'beneficiaries' => $balances->count(),
            'paid_this_month' => (float) Withdrawal::whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            'paid_lifetime' => (float) Withdrawal::sum('amount'),
        ];

        return view('admin.hierarchy.withdrawals.index', compact('withdrawals', 'balances', 'summary'));
    }

    public function create(Request $request)
    {
        $users = User::whereIn('role', ['manager', 'rm', 'affiliate'])
            ->orderBy('role')->orderBy('name')
            ->get(['id', 'name', 'mobile', 'role']);

        $user = null;
        $approvedBalance = 0.0;
        $pendingTotal = 0.0;
        $paidLifetime = 0.0;
        $approvedCommissions = collect();
        $profile = null;

        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
            if ($user) {
                $profile = $user->affiliateProfile;
                $approvedBalance = (float) Commission::forUser($user->id)->approved()->sum('amount');
                $pendingTotal    = (float) Commission::forUser($user->id)->pending()->sum('amount');
                $paidLifetime    = (float) Commission::forUser($user->id)->paid()->sum('amount');
                $approvedCommissions = Commission::with('order:id,order_number')
                    ->where('beneficiary_user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->get();
            }
        }

        return view('admin.hierarchy.withdrawals.create', compact(
            'users', 'user', 'profile', 'approvedBalance', 'pendingTotal', 'paidLifetime', 'approvedCommissions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'amount'    => 'required|numeric|min:0.01',
            'method'    => 'required|in:bank,upi,cash,other',
            'reference' => 'nullable|string|max:255',
            'notes'     => 'nullable|string|max:2000',
            'paid_at'   => 'nullable|date',
        ]);

        $approvedBalance = (float) Commission::forUser($validated['user_id'])->approved()->sum('amount');
        $amount = (float) $validated['amount'];

        if ($amount > $approvedBalance + 0.001) {
            return back()->withInput()->with('error', "Amount ₹{$amount} exceeds approved balance ₹{$approvedBalance}.");
        }

        DB::transaction(function () use ($validated, $amount, &$withdrawal) {
            $withdrawal = Withdrawal::create([
                'user_id'    => $validated['user_id'],
                'amount'     => $amount,
                'method'     => $validated['method'],
                'reference'  => $validated['reference'] ?? null,
                'notes'      => $validated['notes'] ?? null,
                'paid_at'    => $validated['paid_at'] ?? now(),
                'created_by' => Auth::id(),
            ]);

            // Mark approved commissions as paid (oldest first) up to the amount
            $remaining = $amount;
            $approvedRows = Commission::where('beneficiary_user_id', $validated['user_id'])
                ->where('status', 'approved')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get();

            foreach ($approvedRows as $c) {
                if ($remaining <= 0.001) break;
                $commAmt = (float) $c->amount;
                if ($commAmt <= $remaining + 0.001) {
                    $c->update([
                        'status'         => 'paid',
                        'paid_at'        => $withdrawal->paid_at,
                        'payout_ref'     => $withdrawal->reference,
                        'withdrawal_id'  => $withdrawal->id,
                    ]);
                    $remaining -= $commAmt;
                }
            }
        });

        return redirect()->route('admin.withdrawals.show', $withdrawal)->with('success', 'Withdrawal recorded successfully.');
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['user:id,name,mobile,email,role', 'creator:id,name', 'commissions.order:id,order_number']);
        $profile = $withdrawal->user?->affiliateProfile;
        return view('admin.hierarchy.withdrawals.show', compact('withdrawal', 'profile'));
    }

    public function destroy(Withdrawal $withdrawal)
    {
        DB::transaction(function () use ($withdrawal) {
            // Revert linked commissions back to approved
            Commission::where('withdrawal_id', $withdrawal->id)->update([
                'status'        => 'approved',
                'paid_at'       => null,
                'payout_ref'    => null,
                'withdrawal_id' => null,
            ]);
            $withdrawal->delete();
        });

        return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal reversed. Commissions returned to approved status.');
    }
}
