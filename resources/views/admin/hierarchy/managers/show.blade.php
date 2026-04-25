@extends('layouts.admin')

@section('title', 'Manager: ' . ($manager->name ?? 'User #'.$manager->id))

@section('content')
@php
    $initials = collect(explode(' ', trim($manager->name ?? 'M')))
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->take(2)->implode('');
    $statusBadge = [
        'pending'  => 'bg-amber-100 text-amber-700 ring-amber-200',
        'approved' => 'bg-green-100 text-green-700 ring-green-200',
        'rejected' => 'bg-red-100 text-red-700 ring-red-200',
        'paid'     => 'bg-green-100 text-green-700 ring-green-200',
        'reversed' => 'bg-gray-200 text-gray-600 ring-gray-200',
        'none'     => 'bg-gray-100 text-gray-600 ring-gray-200',
    ];
@endphp

<div class="space-y-5">

    {{-- Breadcrumb --}}
    <a href="{{ route('admin.managers.index') }}" class="text-sm text-text-muted hover:text-text-heading inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Managers
    </a>

    {{-- Hero / Header --}}
    <div class="bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-2xl shadow-sm p-6 text-white">
        <div class="flex flex-wrap items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/20 ring-2 ring-white/30 flex items-center justify-center text-2xl font-bold shrink-0">
                {{ strtoupper($initials) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 uppercase tracking-wider ring-1 ring-white/30">Manager</span>
                    @if($manager->is_verified)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 uppercase tracking-wider ring-1 ring-white/30">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Verified
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-bold truncate">{{ $manager->name ?? 'User #'.$manager->id }}</h1>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 mt-2 text-sm opacity-90">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $manager->mobile ?? '—' }}
                    </span>
                    <span class="opacity-40 text-xs">·</span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $manager->email ?? '—' }}
                    </span>
                    <span class="opacity-40 text-xs">·</span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Joined <strong class="ml-1 font-semibold">{{ $manager->created_at?->format('d M Y') }}</strong>
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.managers.edit', $manager) }}" class="bg-white/15 hover:bg-white/25 text-white border border-white/30 px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-1.5 backdrop-blur">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>@endif

    {{-- KPI strip --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">RMs</div>
                <div class="text-xl font-bold text-text-heading leading-tight">{{ $summary['rms_count'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Affiliates</div>
                <div class="text-xl font-bold text-text-heading leading-tight">{{ $summary['affiliates_count'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-brand-gold/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Lifetime Earnings</div>
                <div class="text-xl font-bold text-brand-gold leading-tight">&#8377;{{ number_format($managerTotals['lifetime'], 0) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Wallet Balance</div>
                <div class="text-xl font-bold text-green-600 leading-tight">&#8377;{{ number_format($managerTotals['wallet'], 0) }}</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-ui-border">
        <nav class="flex gap-1 -mb-px overflow-x-auto" id="mgr-tabs" role="tablist">
            <button type="button" data-tab="rms" class="mgr-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-brand-gold text-brand-gold inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                RMs
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-brand-gold/15 text-brand-gold text-[10px] font-bold">{{ $summary['rms_count'] }}</span>
            </button>
            <button type="button" data-tab="wallet" class="mgr-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Wallet History
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $walletTransactions->count() }}</span>
            </button>
            <button type="button" data-tab="orders" class="mgr-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Order History
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $summary['orders_count'] }}</span>
            </button>
        </nav>
    </div>

    {{-- Tab panel: RMs --}}
    <div data-panel="rms" class="mgr-tab-panel">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
                <h3 class="font-semibold text-text-heading flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Relationship Managers
                </h3>
                <span class="text-xs text-text-muted">{{ $summary['rms_count'] }} RM(s) · {{ $summary['affiliates_count'] }} total affiliate(s)</span>
            </div>
            @if($rms->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold">RM</th>
                                <th class="text-left px-4 py-3 font-semibold">Mobile</th>
                                <th class="text-right px-4 py-3 font-semibold">Affiliates</th>
                                <th class="text-right px-4 py-3 font-semibold">Lifetime Earnings</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rms as $rm)
                                @php
                                    $rmOwn = $rmCommissionTotals[$rm->id] ?? [];
                                    $rmOwnLifetime = collect($rmOwn)->filter(fn($v, $k) => $k !== 'reversed')->sum();
                                    $rmInitial = strtoupper(mb_substr($rm->name ?? 'R', 0, 1));
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold shrink-0">{{ $rmInitial }}</div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-600 text-white uppercase tracking-wider">RM</span>
                                                    <span class="font-semibold text-text-heading truncate">{{ $rm->name ?? 'User #'.$rm->id }}</span>
                                                </div>
                                                @if($rm->email)<div class="text-xs text-text-muted truncate">{{ $rm->email }}</div>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-text-muted">{{ $rm->mobile }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-xs font-bold">{{ $rm->affiliates_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums {{ $rmOwnLifetime > 0 ? 'text-brand-gold font-bold' : 'text-gray-400' }}">&#8377;{{ number_format($rmOwnLifetime, 2) }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.rms.show', $rm) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-xs font-semibold">
                                            View <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <p class="text-sm text-text-muted">No RMs assigned to this manager yet.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tab panel: Wallet --}}
    <div data-panel="wallet" class="mgr-tab-panel hidden space-y-5">

        {{-- Current balance summary --}}
        <div style="display:flex;gap:1rem;">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center flex-1">
                <div class="text-xs text-green-700 font-bold uppercase tracking-wider mb-1">Total Added</div>
                <div class="text-2xl font-bold text-green-700 tabular-nums">&#8377;{{ number_format($walletTransactions->where('type','credit')->sum('amount'), 2) }}</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center flex-1">
                <div class="text-xs text-red-700 font-bold uppercase tracking-wider mb-1">Total Removed</div>
                <div class="text-2xl font-bold text-red-700 tabular-nums">&#8377;{{ number_format($walletTransactions->where('type','debit')->sum('amount'), 2) }}</div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center flex-1">
                <div class="text-xs text-blue-700 font-bold uppercase tracking-wider mb-1">Wallet Balance</div>
                <div class="text-2xl font-bold text-blue-700 tabular-nums">&#8377;{{ number_format($managerTotals['wallet'], 2) }}</div>
            </div>
        </div>

        {{-- Add / Remove form --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
            <h3 class="font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Wallet Transaction
            </h3>
            <form method="POST" action="{{ route('admin.managers.wallet', $manager) }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-text-muted mb-1">Type *</label>
                    <select name="type" id="mgr_wallet_type" required onchange="mgrToggleReqDir(this)"
                            class="px-3 py-2 border border-ui-border rounded-lg text-sm bg-white min-w-[140px]">
                        <option value="credit">➕ Add (Credit)</option>
                        <option value="debit">➖ Remove (Debit)</option>
                        <option value="request">📋 Request</option>
                    </select>
                </div>
                <div id="mgr_req_dir" class="hidden">
                    <label class="block text-xs font-medium text-text-muted mb-1">Direction *</label>
                    <select name="request_direction" id="mgr_req_dir_select"
                            class="px-3 py-2 border border-ui-border rounded-lg text-sm bg-white min-w-[140px]">
                        <option value="credit">Credit (Receive)</option>
                        <option value="debit">Debit (Send)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-muted mb-1">Amount (₹) *</label>
                    <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0.00"
                           class="px-3 py-2 border border-ui-border rounded-lg text-sm w-36 tabular-nums">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-text-muted mb-1">Remark</label>
                    <input type="text" name="remark" placeholder="Reason for this transaction (optional)" maxlength="500"
                           class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
                </div>
                <button type="submit"
                    class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm whitespace-nowrap">
                    Apply
                </button>
            </form>
            <script>
            function mgrToggleReqDir(sel) {
                var div = document.getElementById('mgr_req_dir');
                var s   = document.getElementById('mgr_req_dir_select');
                var show = sel.value === 'request';
                div.classList.toggle('hidden', !show);
                s.required = show;
            }
            </script>
        </div>

        {{-- Transaction history --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
                <h3 class="font-semibold text-text-heading">Transaction History</h3>
                <span class="text-xs text-text-muted">{{ $walletTransactions->count() }} transaction(s)</span>
            </div>
            @if($walletTransactions->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold">Type</th>
                                <th class="text-right px-4 py-3 font-semibold">Amount</th>
                                <th class="text-left px-4 py-3 font-semibold">Status</th>
                                <th class="text-left px-4 py-3 font-semibold">Remark</th>
                                <th class="text-left px-4 py-3 font-semibold">By</th>
                                <th class="text-left px-4 py-3 font-semibold">Date</th>
                                <th class="text-left px-4 py-3 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($walletTransactions as $tx)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3">
                                        @if($tx->type === 'credit')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>Credit
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>Debit
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $tx->type === 'credit' ? 'text-green-700' : 'text-red-600' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}&#8377;{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($tx->status === 'approved')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase">Approved</span>
                                        @elseif($tx->status === 'pending')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 uppercase">Pending</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-text-muted">{{ $tx->remark ?: '—' }}</td>
                                    <td class="px-4 py-3 text-text-muted text-xs">{{ optional($tx->creator)->name ?? 'Admin' }}</td>
                                    <td class="px-4 py-3 text-text-muted text-xs">{{ $tx->created_at?->format('d M Y, h:i A') }}</td>
                                    <td class="px-4 py-3">
                                        @if($tx->status === 'pending')
                                            <div class="flex items-center gap-1">
                                                <form method="POST" action="{{ route('admin.managers.wallet.approve', $tx) }}" class="inline">
                                                    @csrf
                                                    <button class="px-2 py-1 text-[10px] font-bold bg-green-600 hover:bg-green-700 text-white rounded">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.managers.wallet.reject', $tx) }}" class="inline">
                                                    @csrf
                                                    <button class="px-2 py-1 text-[10px] font-bold bg-red-600 hover:bg-red-700 text-white rounded">Reject</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-text-muted text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center text-sm text-text-muted">No wallet transactions yet.</div>
            @endif
        </div>
    </div>

    {{-- Tab panel: Order History --}}
    <div data-panel="orders" class="mgr-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
                <h3 class="font-semibold text-text-heading flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Order History
                </h3>
                <span class="text-xs text-text-muted">{{ $summary['orders_count'] }} order(s) in hierarchy</span>
            </div>
            @if($orderHistory->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold">Order #</th>
                                <th class="text-left px-4 py-3 font-semibold">Date</th>
                                <th class="text-left px-4 py-3 font-semibold">Customer</th>
                                <th class="text-left px-4 py-3 font-semibold">Affiliate</th>
                                <th class="text-left px-4 py-3 font-semibold">RM</th>
                                <th class="text-right px-4 py-3 font-semibold">Order Total</th>
                                <th class="text-right px-4 py-3 font-semibold">Mgr Earnings</th>
                                <th class="text-left px-4 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($orderHistory as $order)
                                @php
                                    $commMap = $orderCommissionMap[$order->id] ?? [];
                                    $affComm = $commMap['affiliate'] ?? null;
                                    $rmComm  = $commMap['rm'] ?? null;
                                    $mgrComm = $commMap['manager'] ?? null;
                                    $affName = $affComm ? ($affiliateNameMap[$affComm['user_id']] ?? '—') : '—';
                                    $affRmId = $affComm ? ($affiliateRmMap[$affComm['user_id']] ?? null) : null;
                                    $rmName  = $rmComm  ? ($rmNameMap[$rmComm['user_id']] ?? '—')
                                                        : ($affRmId ? ($rmNameMap[$affRmId] ?? '—') : '—');
                                    $orderStatusColors = [
                                        'pending'   => 'bg-amber-100 text-amber-700',
                                        'confirmed' => 'bg-blue-100 text-blue-700',
                                        'shipped'   => 'bg-indigo-100 text-indigo-700',
                                        'delivered' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                    $osBadge = $orderStatusColors[$order->order_status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs font-semibold text-text-heading">{{ $order->order_number ?? '#'.$order->id }}</td>
                                    <td class="px-4 py-3 text-text-muted text-xs whitespace-nowrap">{{ $order->created_at?->format('d M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-text-heading text-xs">{{ $order->name ?? '—' }}</div>
                                        <div class="text-[11px] text-text-muted">{{ $order->mobile ?? '—' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-text-muted">{{ $affName }}</td>
                                    <td class="px-4 py-3 text-xs text-text-muted">{{ $rmName }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-semibold text-text-heading">&#8377;{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if($mgrComm)
                                            <div class="tabular-nums text-xs font-semibold {{ $mgrComm['status'] === 'reversed' ? 'text-gray-400 line-through' : 'text-brand-gold' }}">&#8377;{{ number_format($mgrComm['amount'], 2) }}</div>
                                        @else
                                            <form method="POST" action="{{ route('admin.orders.backfill-commissions', $order) }}" class="inline">
                                                @csrf
                                                <button type="submit" title="Recalculate missing commissions"
                                                    class="text-[10px] text-amber-600 hover:text-amber-800 underline font-semibold">
                                                    Recalculate
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $osBadge }}">{{ $order->order_status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm text-text-muted">No orders found in this manager's hierarchy yet.</p>
                </div>
            @endif
        </div>
    </div>

</div>

<script>
(function(){
    const btns = document.querySelectorAll('.mgr-tab-btn');
    const panels = document.querySelectorAll('.mgr-tab-panel');
    const activeCls = ['border-brand-gold', 'text-brand-gold'];
    const idleCls = ['border-transparent', 'text-text-muted', 'hover:text-text-heading'];

    function activate(tab) {
        btns.forEach(b => {
            const on = b.dataset.tab === tab;
            activeCls.forEach(c => b.classList.toggle(c, on));
            idleCls.forEach(c => b.classList.toggle(c, !on));
        });
        panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== tab));
        try { history.replaceState(null, '', '#' + tab); } catch (e) {}
    }

    btns.forEach(b => b.addEventListener('click', () => activate(b.dataset.tab)));

    const initial = (location.hash || '').replace('#', '');
    if (initial && document.querySelector('[data-panel="' + initial + '"]')) {
        activate(initial);
    }
})();
</script>
@endsection
