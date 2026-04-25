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

    {{-- KPI strip: Total, Manager, RM, Affiliate --}}
    <div style="display:flex;gap:0.75rem;margin-bottom:1.25rem;">
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden flex-1">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#1f2937;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#f3f4f6;">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-700">Total Amount</div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($allTotal, 2) }}</div>
            <div class="text-[10px] text-text-muted mt-1">{{ $allCount }} entries</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden flex-1">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#4338ca;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#ede9fe;">
                    <svg class="w-5 h-5" style="color:#4338ca;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#4338ca;">Manager</div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($roleTotals['manager']['total'] ?? 0, 2) }}</div>
            <div class="text-[10px] text-text-muted mt-1">{{ $roleTotals['manager']['count'] ?? 0 }} entries</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden flex-1">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#1d4ed8;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#dbeafe;">
                    <svg class="w-5 h-5" style="color:#1d4ed8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#1d4ed8;">RM Amount</div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($roleTotals['rm']['total'] ?? 0, 2) }}</div>
            <div class="text-[10px] text-text-muted mt-1">{{ $roleTotals['rm']['count'] ?? 0 }} entries</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden flex-1">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#d97706;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#fef3c7;">
                    <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#d97706;">Affiliate</div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($roleTotals['affiliate']['total'] ?? 0, 2) }}</div>
            <div class="text-[10px] text-text-muted mt-1">{{ $roleTotals['affiliate']['count'] ?? 0 }} entries</div>
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
