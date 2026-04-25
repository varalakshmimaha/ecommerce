@extends('layouts.frontend')

@section('title', 'My Earnings')

@section('content')
<section class="bg-gradient-to-br from-gray-50 to-white py-10 min-h-screen">
    <div class="container mx-auto px-4">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-text-muted hover:text-brand-gold transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-text-heading">My Earnings</h1>
                <p class="text-text-muted mt-1 text-sm">
                    Signed in as <span class="font-semibold">{{ $user->name ?? $user->mobile }}</span>
                    <span class="ml-2 inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-brand-gold/10 text-brand-gold uppercase">{{ $user->role }}</span>
                </p>
            </div>
            @if($referralUrl)
                <div class="mt-4 md:mt-0 bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="text-xs text-text-muted mb-1">Your referral code</div>
                    <div class="flex items-center gap-2">
                        <code class="font-mono font-bold text-brand-gold text-lg">{{ $user->referral_code }}</code>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $referralUrl }}'); this.textContent='Copied!'"
                                class="text-xs px-3 py-1 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white rounded-md font-semibold">
                            Copy Link
                        </button>
                    </div>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="text-xs text-text-muted uppercase font-semibold tracking-wide">Pending</div>
                <div class="text-2xl font-bold text-amber-600 mt-1">&#8377;{{ number_format($totals['pending'], 2) }}</div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="text-xs text-text-muted uppercase font-semibold tracking-wide">Approved</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">&#8377;{{ number_format($totals['approved'], 2) }}</div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="text-xs text-text-muted uppercase font-semibold tracking-wide">Paid</div>
                <div class="text-2xl font-bold text-green-600 mt-1">&#8377;{{ number_format($totals['paid'], 2) }}</div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="text-xs text-text-muted uppercase font-semibold tracking-wide">Lifetime</div>
                <div class="text-2xl font-bold text-text-heading mt-1">&#8377;{{ number_format($totals['lifetime'], 2) }}</div>
            </div>
        </div>

        {{-- Wallet Request --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Wallet Request
            </h2>
            <form method="POST" action="{{ route('affiliate.wallet-request.store') }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-text-muted mb-1">Direction *</label>
                    <select name="direction" required
                            class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white min-w-[150px] focus:outline-none focus:ring-2 focus:ring-brand-gold">
                        <option value="credit">Credit (Receive)</option>
                        <option value="debit">Debit (Send)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-muted mb-1">Amount (₹) *</label>
                    <input type="number" name="amount" min="1" step="0.01" required placeholder="0.00"
                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-36 tabular-nums focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-text-muted mb-1">Remark (optional)</label>
                    <input type="text" name="remark" placeholder="Reason for request" maxlength="500"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <button type="submit"
                        class="px-5 py-2 rounded-lg font-semibold text-sm text-white whitespace-nowrap bg-brand-gold hover:opacity-90">
                    Submit Request
                </button>
            </form>
            @if($walletTransactions->count())
                <div class="mt-5 border-t border-gray-100 pt-4">
                    <div class="text-xs text-text-muted uppercase font-semibold tracking-wide mb-3">My Wallet Transactions</div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-text-muted text-[10px] uppercase tracking-wider">
                                <tr>
                                    <th class="text-left py-2">Date</th>
                                    <th class="text-left py-2 pl-3">Type</th>
                                    <th class="text-right py-2">Amount</th>
                                    <th class="text-left py-2 pl-3">Status</th>
                                    <th class="text-left py-2 pl-3">Remark</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($walletTransactions as $wt)
                                    @php
                                        $stBadge = ['approved'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','rejected'=>'bg-red-100 text-red-700'][$wt->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <tr>
                                        <td class="py-2 text-xs text-text-muted">{{ $wt->created_at?->format('d M Y') }}</td>
                                        <td class="py-2 pl-3">
                                            @if($wt->type === 'credit')
                                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase">Credit</span>
                                            @else
                                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase">Debit</span>
                                            @endif
                                        </td>
                                        <td class="py-2 text-right tabular-nums font-semibold {{ $wt->type === 'credit' ? 'text-green-700' : 'text-red-600' }}">
                                            {{ $wt->type === 'credit' ? '+' : '-' }}&#8377;{{ number_format($wt->amount, 2) }}
                                        </td>
                                        <td class="py-2 pl-3"><span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $stBadge }}">{{ $wt->status }}</span></td>
                                        <td class="py-2 pl-3 text-xs text-text-muted">{{ $wt->remark ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Wallet Balance + Withdrawal Request --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <div>
                    <div class="text-xs text-text-muted uppercase font-semibold tracking-wide mb-1">Wallet Balance</div>
                    <div class="text-3xl font-bold text-green-600 tabular-nums">&#8377;{{ number_format($walletBalance, 2) }}</div>
                </div>
                @if($hasPendingRequest)
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Withdrawal Request Pending
                    </div>
                @endif
            </div>

            @if(!$hasPendingRequest && $walletBalance > 0)
                <form method="POST" action="{{ route('affiliate.withdrawal-request.store') }}" class="flex flex-wrap gap-3 items-end border-t border-gray-100 pt-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-text-muted mb-1">Withdraw Amount (₹) *</label>
                        <input type="number" name="amount" min="1" step="0.01" max="{{ $walletBalance }}" required
                               placeholder="0.00"
                               class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-40 tabular-nums focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-text-muted mb-1">Note (optional)</label>
                        <input type="text" name="notes" placeholder="Any note for admin" maxlength="500"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg font-semibold text-sm text-white whitespace-nowrap"
                            style="background-color:#16a34a;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Request Withdrawal
                    </button>
                </form>
            @elseif($walletBalance <= 0)
                <div class="border-t border-gray-100 pt-4 text-sm text-text-muted">No wallet balance available for withdrawal.</div>
            @endif

            @if($withdrawalRequests->count())
                <div class="mt-5 border-t border-gray-100 pt-4">
                    <div class="text-xs text-text-muted uppercase font-semibold tracking-wide mb-3">My Withdrawal Requests</div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-text-muted text-[10px] uppercase tracking-wider">
                                <tr>
                                    <th class="text-left py-2">Date</th>
                                    <th class="text-right py-2">Amount</th>
                                    <th class="text-left py-2 pl-4">Status</th>
                                    <th class="text-left py-2 pl-4">Note</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($withdrawalRequests as $wr)
                                    @php
                                        $wrBadge = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$wr->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <tr>
                                        <td class="py-2 text-xs text-text-muted">{{ $wr->created_at?->format('d M Y') }}</td>
                                        <td class="py-2 text-right tabular-nums font-semibold">&#8377;{{ number_format($wr->amount, 2) }}</td>
                                        <td class="py-2 pl-4"><span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $wrBadge }}">{{ $wr->status }}</span></td>
                                        <td class="py-2 pl-4 text-xs text-text-muted">
                                            @if($wr->status === 'rejected' && $wr->rejection_reason)
                                                <span class="text-red-600">{{ $wr->rejection_reason }}</span>
                                            @else
                                                {{ $wr->notes ?: '—' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        @if($user->role !== 'affiliate' && $referralsCount > 0)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-8">
                <div class="text-xs text-text-muted uppercase font-semibold tracking-wide">Referrals ({{ $user->role === 'manager' ? 'RMs / Affiliates' : 'Affiliates' }})</div>
                <div class="text-2xl font-bold text-text-heading mt-1">{{ $referralsCount }}</div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-text-heading">Recent Commissions</h2>
            </div>

            @if($commissions->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Order</th>
                            <th class="text-left px-5 py-3">Date</th>
                            <th class="text-left px-5 py-3">Role</th>
                            <th class="text-right px-5 py-3">Base</th>
                            <th class="text-right px-5 py-3">Rate</th>
                            <th class="text-right px-5 py-3">Amount</th>
                            <th class="text-left px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($commissions as $c)
                            <tr>
                                <td class="px-5 py-3 font-mono text-xs">{{ optional($c->order)->order_number ?? '-' }}</td>
                                <td class="px-5 py-3 text-text-muted">{{ $c->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-3 uppercase text-xs font-semibold">{{ $c->beneficiary_role }}</td>
                                <td class="px-5 py-3 text-right">&#8377;{{ number_format($c->base_amount, 2) }}</td>
                                <td class="px-5 py-3 text-right">{{ rtrim(rtrim(number_format($c->percentage, 2), '0'), '.') }}%</td>
                                <td class="px-5 py-3 text-right font-semibold">&#8377;{{ number_format($c->amount, 2) }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $badge = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'approved' => 'bg-blue-100 text-blue-700',
                                            'paid' => 'bg-green-100 text-green-700',
                                            'reversed' => 'bg-gray-200 text-gray-600',
                                        ][$c->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }} uppercase">{{ $c->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">{{ $commissions->links() }}</div>
            @else
                <div class="px-5 py-10 text-center text-text-muted">
                    No commissions yet. Share your referral code to start earning.
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
