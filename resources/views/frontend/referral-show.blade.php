@extends('layouts.frontend')

@section('title', 'Referral: ' . ($referral->name ?? 'User'))

@section('content')
@php
    $initials = collect(explode(' ', trim($referral->name ?? 'U')))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('');
@endphp

<div class="max-w-6xl mx-auto px-4 py-8">
    {{-- Back link --}}
    <a href="{{ route('user.dashboard') }}#referrals" class="inline-flex items-center gap-1 text-sm text-text-muted hover:text-text-heading mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Dashboard
    </a>

    {{-- Hero --}}
    <div class="bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-2xl shadow-lg p-6 text-white mb-6">
        <div class="flex flex-wrap items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/20 ring-2 ring-white/30 flex items-center justify-center text-2xl font-bold shrink-0">
                {{ strtoupper($initials) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 uppercase tracking-wider ring-1 ring-white/30">{{ $referral->role }}</span>
                    @if($referral->referral_code)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 uppercase tracking-wider ring-1 ring-white/30 font-mono">{{ $referral->referral_code }}</span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-bold truncate">{{ $referral->name ?? 'User #'.$referral->id }}</h1>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm opacity-90">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $referral->mobile }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Joined {{ $referral->created_at?->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#3b82f6;"></div>
            <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#1d4ed8;">Orders</div>
            <div class="text-2xl font-bold text-text-heading tabular-nums mt-2">{{ $ordersCount }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#6b7280;"></div>
            <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#374151;">Base</div>
            <div class="text-2xl font-bold text-text-heading tabular-nums mt-2">&#8377;{{ number_format($baseTotal, 2) }}</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#f59e0b;"></div>
            <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#b45309;">My Rate</div>
            <div class="text-2xl font-bold text-text-heading tabular-nums mt-2">{{ $myRate }}%</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-5 overflow-hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#ea580c;"></div>
            <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#9a3412;">My Earnings</div>
            <div class="text-2xl font-bold tabular-nums mt-2" style="color:#9a3412;">&#8377;{{ number_format($commTotal, 2) }}</div>
        </div>
    </div>

    {{-- Sub-referrals --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="p-2.5 bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-xl shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-text-heading leading-tight">{{ $referral->name ?? 'Their' }}'s Affiliates</h2>
                <p class="text-xs text-text-muted">{{ $subReferrals->count() }} {{ $subReferrals->count() === 1 ? 'person' : 'people' }} referred by {{ $referral->name ?? 'this user' }}</p>
            </div>
        </div>

        @if($subReferrals->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Name</th>
                            <th class="text-left px-5 py-3 font-semibold">Mobile</th>
                            <th class="text-left px-5 py-3 font-semibold">Role</th>
                            <th class="text-left px-5 py-3 font-semibold">Ref Code</th>
                            <th class="text-right px-5 py-3 font-semibold">Orders</th>
                            <th class="text-left px-5 py-3 font-semibold">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($subReferrals as $sub)
                            @php $subInitial = strtoupper(mb_substr($sub->name ?? 'U', 0, 1)); @endphp
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-gold/10 text-brand-gold flex items-center justify-center text-xs font-bold shrink-0">{{ $subInitial }}</div>
                                        <div class="font-semibold text-text-heading">{{ $sub->name ?? '—' }}</div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-text-muted">{{ $sub->mobile }}</td>
                                <td class="px-5 py-3 uppercase text-xs font-semibold">{{ $sub->role }}</td>
                                <td class="px-5 py-3 font-mono text-xs text-text-muted">{{ $sub->referral_code ?? '—' }}</td>
                                <td class="px-5 py-3 text-right tabular-nums">{{ $sub->orders_count ?? 0 }}</td>
                                <td class="px-5 py-3 text-text-muted text-xs">{{ $sub->created_at?->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="inline-flex p-4 rounded-full bg-gray-100 mb-3">
                    <svg class="w-8 h-8 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <p class="text-sm text-text-muted">{{ $referral->name ?? 'This user' }} hasn't referred anyone yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
