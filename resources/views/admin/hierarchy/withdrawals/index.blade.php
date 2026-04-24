@extends('layouts.admin')

@section('title', 'Withdrawals')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-heading">Withdrawals</h1>
            <p class="text-text-muted mt-1">Record commission payouts to managers, RMs, and affiliates.</p>
        </div>
        <a href="{{ route('admin.withdrawals.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2.5 rounded-lg font-semibold shadow">+ Record Withdrawal</a>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>@endif

    {{-- KPI strip --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#3b82f6;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#dbeafe;">
                    <svg class="w-5 h-5" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div><div class="text-[11px] font-bold uppercase tracking-wider" style="color:#1d4ed8;">Outstanding</div><div class="text-[10px] text-text-muted">Ready to pay</div></div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($summary['outstanding'], 2) }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#8b5cf6;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#ede9fe;">
                    <svg class="w-5 h-5" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div><div class="text-[11px] font-bold uppercase tracking-wider" style="color:#6d28d9;">Beneficiaries</div><div class="text-[10px] text-text-muted">Awaiting payout</div></div>
            </div>
            <div class="text-2xl font-bold text-text-heading">{{ $summary['beneficiaries'] }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#10b981;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#d1fae5;">
                    <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><div class="text-[11px] font-bold uppercase tracking-wider" style="color:#047857;">This Month</div><div class="text-[10px] text-text-muted">Paid out</div></div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($summary['paid_this_month'], 2) }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#f59e0b;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#fef3c7;">
                    <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div><div class="text-[11px] font-bold uppercase tracking-wider" style="color:#b45309;">Lifetime</div><div class="text-[10px] text-text-muted">All payouts</div></div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($summary['paid_lifetime'], 2) }}</div>
        </div>
    </div>

    {{-- Outstanding balances table --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
            <h3 class="font-semibold text-text-heading">Outstanding Balances</h3>
            <span class="text-xs text-text-muted">Approved commissions awaiting payout</span>
        </div>
        @if($balances->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-3">Beneficiary</th>
                            <th class="text-left px-4 py-3">Mobile</th>
                            <th class="text-left px-4 py-3">Role</th>
                            <th class="text-right px-4 py-3">Entries</th>
                            <th class="text-right px-4 py-3">Approved Balance</th>
                            <th class="text-left px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($balances as $b)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 font-semibold">{{ optional($b->beneficiary)->name ?? 'User #'.$b->beneficiary_user_id }}</td>
                                <td class="px-4 py-3">{{ optional($b->beneficiary)->mobile ?? '—' }}</td>
                                <td class="px-4 py-3 uppercase text-xs font-semibold">{{ $b->beneficiary_role }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $b->rows_cnt }}</td>
                                <td class="px-4 py-3 text-right tabular-nums font-bold text-blue-700">&#8377;{{ number_format($b->approved_total, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.withdrawals.create', ['user_id' => $b->beneficiary_user_id]) }}" style="background-color:#16a34a;color:#ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold hover:opacity-90">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                        Pay Now
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-10 text-center text-sm text-text-muted">No outstanding balances. All approved commissions are paid.</div>
        @endif
    </div>

    {{-- Filter form --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-ui-border p-4 mb-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-text-muted mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Beneficiary name or mobile" class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-text-muted mb-1">Method</label>
            <select name="method" class="px-3 py-2 border border-ui-border rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['bank','upi','cash','other'] as $m)
                    <option value="{{ $m }}" {{ request('method')===$m?'selected':'' }}>{{ strtoupper($m) }}</option>
                @endforeach
            </select>
        </div>
        <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm">Filter</button>
        @if(request()->hasAny(['search','method']))
            <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-text-muted hover:text-text-heading">Reset</a>
        @endif
    </form>

    {{-- Past withdrawals ledger --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-5 py-4 border-b border-ui-border">
            <h3 class="font-semibold text-text-heading">Withdrawal History</h3>
        </div>
        @if($withdrawals->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-3">Date</th>
                            <th class="text-left px-4 py-3">Beneficiary</th>
                            <th class="text-left px-4 py-3">Role</th>
                            <th class="text-left px-4 py-3">Method</th>
                            <th class="text-left px-4 py-3">Reference</th>
                            <th class="text-right px-4 py-3">Amount</th>
                            <th class="text-left px-4 py-3">By</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($withdrawals as $w)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 text-xs text-text-muted">{{ $w->paid_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3 font-semibold">
                                    {{ optional($w->user)->name ?? 'User #'.$w->user_id }}
                                    <div class="text-xs text-text-muted">{{ optional($w->user)->mobile }}</div>
                                </td>
                                <td class="px-4 py-3 uppercase text-xs font-semibold">{{ optional($w->user)->role ?? '—' }}</td>
                                <td class="px-4 py-3 uppercase text-xs">{{ $w->method }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-text-muted">{{ $w->reference ?? '—' }}</td>
                                <td class="px-4 py-3 text-right tabular-nums font-bold text-green-700">&#8377;{{ number_format($w->amount, 2) }}</td>
                                <td class="px-4 py-3 text-xs text-text-muted">{{ optional($w->creator)->name ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.withdrawals.show', $w) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-ui-border">{{ $withdrawals->links() }}</div>
        @else
            <div class="px-5 py-10 text-center text-sm text-text-muted">No withdrawals recorded yet.</div>
        @endif
    </div>
</div>
@endsection
