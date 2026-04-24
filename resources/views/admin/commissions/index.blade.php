@extends('layouts.admin')

@section('title', 'Commissions')

@section('content')
<div>
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-heading">Commissions</h1>
            <p class="text-text-muted mt-1">Earnings ledger across all affiliates, RMs, and managers.</p>
        </div>
        <a href="{{ route('admin.commissions.export', request()->query()) }}" style="background-color:#16a34a;color:#ffffff;" class="px-5 py-2.5 rounded-lg text-sm font-bold hover:opacity-90 inline-flex items-center gap-2 shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
    @endif

    {{-- Role data --}}
    @php
        $roleMeta = [
            ''          => ['label' => 'All',                 'sub' => 'Everyone',      'accent' => '#1f2937'],
            'manager'   => ['label' => 'Manager',             'sub' => 'Top-tier · 2%', 'accent' => '#4338ca'],
            'rm'        => ['label' => 'Relationship Manager','sub' => 'Mid-tier · 3%', 'accent' => '#1d4ed8'],
            'affiliate' => ['label' => 'Affiliate',           'sub' => 'Sales · 5%',    'accent' => '#d97706'],
        ];
        $activeRole = request('role', '');
        $allTotal    = array_sum(array_column($roleTotals, 'total'));
        $allCount    = array_sum(array_column($roleTotals, 'count'));
        $allPending  = array_sum(array_column($roleTotals, 'pending'));
        $allApproved = array_sum(array_column($roleTotals, 'approved'));
        $allPaid     = array_sum(array_column($roleTotals, 'paid'));
        $activeInfo = $roleMeta[$activeRole] ?? $roleMeta[''];
        $activeData = $activeRole === ''
            ? ['total' => $allTotal, 'pending' => $allPending, 'approved' => $allApproved, 'paid' => $allPaid, 'count' => $allCount]
            : ($roleTotals[$activeRole] ?? ['total' => 0, 'pending' => 0, 'approved' => 0, 'paid' => 0, 'count' => 0]);
    @endphp

    {{-- Role tabs (segmented pills) --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border p-1.5 mb-5 inline-flex flex-wrap gap-1">
        @foreach($roleMeta as $key => $info)
            @php
                $cnt = $key === '' ? $allCount : ($roleTotals[$key]['count'] ?? 0);
                $isActive = $activeRole === $key;
                $queryArgs = array_merge(request()->query(), ['role' => $key, 'page' => 1]);
                if ($key === '') $queryArgs['role'] = null;
                $iconMap = [
                    ''          => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>',
                    'manager'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                    'rm'        => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                    'affiliate' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
                ];
            @endphp
            <a href="{{ route('admin.commissions.index', array_filter($queryArgs, fn($v) => $v !== null && $v !== '')) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-2 whitespace-nowrap transition-all"
               style="{{ $isActive ? 'background-color:'.$info['accent'].';color:#ffffff;box-shadow:0 2px 6px '.$info['accent'].'33;' : 'background:transparent;color:#4b5563;' }}"
               @if(!$isActive)onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'"@endif>
                {!! $iconMap[$key] ?? '' !!}
                <span>{{ $info['label'] }}</span>
                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-[10px] font-bold" style="{{ $isActive ? 'background-color:rgba(255,255,255,0.25);color:#ffffff;' : 'background:#e5e7eb;color:#374151;' }}">{{ $cnt }}</span>
            </a>
        @endforeach
    </div>

    {{-- KPI strip (for active role) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $activeInfo['accent'] }};"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $activeInfo['accent'] }}1a;">
                    <svg class="w-5 h-5" style="color:{{ $activeInfo['accent'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-wider" style="color:{{ $activeInfo['accent'] }};">Total</div>
                    <div class="text-[10px] text-text-muted">{{ $activeData['count'] }} {{ $activeData['count'] === 1 ? 'entry' : 'entries' }}</div>
                </div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums mt-3">&#8377;{{ number_format($activeData['total'], 2) }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#f59e0b;"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#fef3c7;">
                    <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#b45309;">Pending</div>
                    <div class="text-[10px] text-text-muted">Awaiting approval</div>
                </div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums mt-3">&#8377;{{ number_format($activeData['pending'], 2) }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#3b82f6;"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#dbeafe;">
                    <svg class="w-5 h-5" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#1d4ed8;">Approved</div>
                    <div class="text-[10px] text-text-muted">Ready to pay</div>
                </div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums mt-3">&#8377;{{ number_format($activeData['approved'], 2) }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#10b981;"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#d1fae5;">
                    <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#047857;">Paid</div>
                    <div class="text-[10px] text-text-muted">Settled</div>
                </div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums mt-3">&#8377;{{ number_format($activeData['paid'], 2) }}</div>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-ui-border p-4 mb-4 flex flex-wrap items-end gap-3">
        @if(request('role'))<input type="hidden" name="role" value="{{ request('role') }}">@endif
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-text-muted mb-1">Search beneficiary</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or mobile" class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-text-muted mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-ui-border rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['pending','approved','paid','reversed'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.commissions.index', request('role') ? ['role' => request('role')] : []) }}" class="text-sm text-text-muted hover:text-text-heading">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        @if($commissions->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-3">Order</th>
                            <th class="text-left px-4 py-3">Beneficiary</th>
                            <th class="text-left px-4 py-3">Role</th>
                            <th class="text-right px-4 py-3">Base</th>
                            <th class="text-right px-4 py-3">Rate</th>
                            <th class="text-right px-4 py-3">Amount</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-left px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($commissions as $c)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ optional($c->order)->order_number ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ optional($c->beneficiary)->name ?? 'User #'.$c->beneficiary_user_id }}</div>
                                    <div class="text-xs text-text-muted">{{ optional($c->beneficiary)->mobile }}</div>
                                </td>
                                <td class="px-4 py-3 uppercase text-xs font-semibold">{{ $c->beneficiary_role }}</td>
                                <td class="px-4 py-3 text-right">&#8377;{{ number_format($c->base_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ rtrim(rtrim(number_format($c->percentage, 2), '0'), '.') }}%</td>
                                <td class="px-4 py-3 text-right font-semibold">&#8377;{{ number_format($c->amount, 2) }}</td>
                                <td class="px-4 py-3">
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
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($c->status === 'pending')
                                        <form method="POST" action="{{ route('admin.commissions.approve', $c) }}" class="inline">
                                            @csrf
                                            <button class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.commissions.reverse', $c) }}" class="inline ml-2">
                                            @csrf
                                            <button class="text-gray-500 hover:text-gray-700 text-xs font-semibold">Reverse</button>
                                        </form>
                                    @elseif($c->status === 'approved')
                                        <a href="{{ route('admin.withdrawals.create', ['user_id' => $c->beneficiary_user_id]) }}" class="text-green-600 hover:text-green-800 text-xs font-semibold">Record Payout</a>
                                    @elseif($c->status === 'paid')
                                        <span class="text-xs text-text-muted">Paid {{ optional($c->paid_at)->format('d M Y') }}@if($c->withdrawal_id) · <a href="{{ route('admin.withdrawals.show', $c->withdrawal_id) }}" class="text-brand-gold hover:underline">Receipt</a>@endif</span>
                                    @else
                                        <span class="text-xs text-text-muted">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-ui-border">{{ $commissions->links() }}</div>
        @else
            <div class="px-5 py-10 text-center text-text-muted">No commissions found.</div>
        @endif
    </div>
</div>
@endsection
