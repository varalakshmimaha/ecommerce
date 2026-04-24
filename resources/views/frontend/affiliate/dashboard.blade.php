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
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
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
