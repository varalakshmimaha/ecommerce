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
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm opacity-90">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $manager->mobile ?? '—' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $manager->email ?? '—' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Joined {{ $manager->created_at?->format('d M Y') }}
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
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
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
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Orders</div>
                <div class="text-xl font-bold text-text-heading leading-tight">{{ $summary['orders_count'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Pending</div>
                <div class="text-xl font-bold text-amber-600 leading-tight">&#8377;{{ number_format($managerTotals['pending'], 0) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Paid</div>
                <div class="text-xl font-bold text-green-600 leading-tight">&#8377;{{ number_format($managerTotals['paid'], 0) }}</div>
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
            <button type="button" data-tab="commissions" class="mgr-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                Manager's Commissions
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $managerCommissions->count() }}</span>
            </button>
            <button type="button" data-tab="rm-commissions" class="mgr-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                RMs Commissions
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $rmCommissions->count() }}</span>
            </button>
            <button type="button" data-tab="orders" class="mgr-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Referrals Orders
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $referralOrders->count() }}</span>
            </button>
        </nav>
    </div>

    {{-- Tab panel: RMs & Affiliates --}}
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
                                <th class="text-right px-4 py-3 font-semibold">Pending</th>
                                <th class="text-right px-4 py-3 font-semibold">Approved</th>
                                <th class="text-right px-4 py-3 font-semibold">Paid</th>
                                <th class="text-right px-4 py-3 font-semibold">Commission</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rms as $rm)
                                @php
                                    $rmOwn = $rmCommissionTotals[$rm->id] ?? [];
                                    $rmOwnPending  = $rmOwn['pending']  ?? 0;
                                    $rmOwnApproved = $rmOwn['approved'] ?? 0;
                                    $rmOwnPaid     = $rmOwn['paid']     ?? 0;
                                    $rmOwnTotal    = $rmOwnPending + $rmOwnApproved + $rmOwnPaid;
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
                                                @if($rm->email)
                                                    <div class="text-xs text-text-muted truncate">{{ $rm->email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-text-muted">{{ $rm->mobile }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-xs font-bold">{{ $rm->affiliates_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums {{ $rmOwnPending > 0 ? 'text-amber-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($rmOwnPending, 2) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums {{ $rmOwnApproved > 0 ? 'text-blue-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($rmOwnApproved, 2) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums {{ $rmOwnPaid > 0 ? 'text-green-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($rmOwnPaid, 2) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums {{ $rmOwnTotal > 0 ? 'text-brand-gold font-bold' : 'text-gray-400' }}">&#8377;{{ number_format($rmOwnTotal, 2) }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.rms.show', $rm) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-xs font-semibold">
                                            View
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
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
        <p class="text-xs text-text-muted mt-3 italic">Showing each RM's own commission earnings (3% of every downline order). Click <strong>View</strong> on an RM to see the affiliates under them.</p>
    </div>

    {{-- Tab panel: Manager's Commissions --}}
    <div data-panel="commissions" class="mgr-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
                <h3 class="font-semibold text-text-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Manager's Commissions
                </h3>
                <span class="text-[10px] text-text-muted uppercase tracking-wider">Last 25</span>
            </div>
            @if($managerCommissions->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold">Order</th>
                                <th class="text-right px-4 py-2.5 font-semibold">Base</th>
                                <th class="text-right px-4 py-2.5 font-semibold">%</th>
                                <th class="text-right px-4 py-2.5 font-semibold">Amount</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Status</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($managerCommissions as $c)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-2 font-mono text-xs">{{ optional($c->order)->order_number ?? '—' }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums text-text-muted">&#8377;{{ number_format($c->base_amount, 0) }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums text-text-muted text-xs">{{ number_format($c->percentage, 1) }}%</td>
                                    <td class="px-4 py-2 text-right tabular-nums font-semibold">&#8377;{{ number_format($c->amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        @php $cb = $statusBadge[$c->status] ?? 'bg-gray-100 text-gray-600 ring-gray-200'; @endphp
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] uppercase ring-1 {{ $cb }}">{{ $c->status }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-text-muted text-xs">{{ $c->created_at?->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center text-sm text-text-muted">No commissions earned yet.</div>
            @endif
        </div>
    </div>

    {{-- Tab panel: RMs Commissions --}}
    <div data-panel="rm-commissions" class="mgr-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
                <h3 class="font-semibold text-text-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    All RMs Commission Earnings
                </h3>
                <span class="text-[10px] text-text-muted uppercase tracking-wider">Last {{ $rmCommissions->count() }}</span>
            </div>
            @if($rmCommissions->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold">RM</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Order</th>
                                <th class="text-right px-4 py-2.5 font-semibold">Base</th>
                                <th class="text-right px-4 py-2.5 font-semibold">%</th>
                                <th class="text-right px-4 py-2.5 font-semibold">Amount</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Status</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rmCommissions as $c)
                                @php
                                    $rmName = optional($c->beneficiary)->name ?? '—';
                                    $rmInitial = strtoupper(mb_substr($rmName, 0, 1));
                                @endphp
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">{{ $rmInitial }}</div>
                                            <span class="font-medium text-text-heading">{{ $rmName }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ optional($c->order)->order_number ?? '—' }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums text-text-muted">&#8377;{{ number_format($c->base_amount, 0) }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums text-text-muted text-xs">{{ number_format($c->percentage, 1) }}%</td>
                                    <td class="px-4 py-2 text-right tabular-nums font-semibold">&#8377;{{ number_format($c->amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        @php $cb = $statusBadge[$c->status] ?? 'bg-gray-100 text-gray-600 ring-gray-200'; @endphp
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] uppercase ring-1 {{ $cb }}">{{ $c->status }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-text-muted text-xs">{{ $c->created_at?->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center text-sm text-text-muted">No RM commissions yet.</div>
            @endif
        </div>
        <p class="text-xs text-text-muted mt-3 italic">Individual commission rows for every RM under this manager. Each row = 3% of that order's subtotal.</p>
    </div>

    {{-- Tab panel: Referrals Orders --}}
    <div data-panel="orders" class="mgr-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
                <h3 class="font-semibold text-text-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Referrals Orders
                </h3>
                <span class="text-[10px] text-text-muted uppercase tracking-wider">Last 25</span>
            </div>
            @if($referralOrders->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold">Order #</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Customer</th>
                                <th class="text-right px-4 py-2.5 font-semibold">Total</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Status</th>
                                <th class="text-left px-4 py-2.5 font-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($referralOrders as $o)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-2 font-mono text-xs">{{ $o->order_number }}</td>
                                    <td class="px-4 py-2">{{ $o->name }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums font-semibold">&#8377;{{ number_format($o->total_amount, 0) }}</td>
                                    <td class="px-4 py-2 uppercase text-xs text-text-muted">{{ $o->order_status }}</td>
                                    <td class="px-4 py-2 text-text-muted text-xs">{{ $o->created_at?->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center text-sm text-text-muted">No orders from referrals yet.</div>
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
