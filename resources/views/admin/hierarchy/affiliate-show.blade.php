@extends('layouts.admin')

@section('title', 'Affiliate: ' . ($user->name ?? 'User #'.$user->id))

@section('content')
@php
    $profile = $user->affiliateProfile;
    $referralLink = $user->referral_code ? url('/user/register?ref=' . $user->referral_code) : null;
    $statusBadge = [
        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
        'approved' => 'bg-green-100 text-green-700 border-green-200',
        'rejected' => 'bg-red-100 text-red-700 border-red-200',
        'none' => 'bg-gray-100 text-gray-600 border-gray-200',
    ][$user->affiliate_status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
    $isAdmin    = auth()->user()->is_admin;
    $isSelf     = auth()->id() === $user->id;
    $aPerms     = $user->permissions ?? [];
    $showRefTab = $isAdmin || ($isSelf && ($aPerms['show_referrals'] ?? true) !== false);
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.affiliates.index') }}" class="text-sm text-text-muted hover:text-text-heading inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Affiliates
        </a>
        <div class="flex flex-wrap items-start justify-between gap-4 mt-2">
            <div>
                <h1 class="text-3xl font-bold text-text-heading">{{ $user->name ?? 'User #'.$user->id }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-gold/10 text-brand-gold uppercase border border-brand-gold/20">{{ $user->role }}</span>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusBadge }} uppercase border">{{ $user->affiliate_status }}</span>
                    @if($profile && $profile->kyc_verified)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            KYC Verified
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.affiliates.edit', $user) }}" class="bg-white hover:bg-gray-50 text-text-heading border border-ui-border px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.affiliates.destroy', $user) }}" class="inline" onsubmit="return confirm('Demote this affiliate to customer? Commissions and referrals are preserved.')">
                    @csrf @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>@endif

    @if(auth()->user()->is_admin)
    @php $earningsRefOn = ($aPerms['show_earnings_referrals'] ?? true) !== false; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-ui-border px-5 py-4 flex flex-wrap items-center gap-4">
        <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Feature Access</span>
        <div class="flex items-center gap-3 flex-1 min-w-[200px] justify-between p-3 rounded-lg border {{ $earningsRefOn ? 'border-orange-200 bg-orange-50' : 'border-gray-200 bg-gray-50' }}" id="aff-earnings-ref-card">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 {{ $earningsRefOn ? 'text-brand-gold' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <div>
                    <div class="text-xs font-semibold text-text-heading">Monthly Earnings &amp; My Referrals</div>
                    <div class="text-[10px] text-text-muted">Show earnings &amp; referrals content on frontend</div>
                </div>
            </div>
            <button type="button" id="toggle-aff-earnings-ref" data-user="{{ $user->id }}" data-section="show_earnings_referrals" data-state="{{ $earningsRefOn ? 'on' : 'off' }}" onclick="toggleAffSection(this)"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $earningsRefOn ? 'bg-brand-gold' : 'bg-gray-300' }}">
                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $earningsRefOn ? 'translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </div>
    </div>
    @endif

    {{-- Referral Card --}}
    @if($referralLink)
        <div class="bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-xl shadow-sm p-6 text-white">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="text-xs uppercase tracking-wider opacity-80 font-semibold mb-1">Referral Code</div>
                    <div class="font-mono font-bold text-3xl">{{ $user->referral_code }}</div>
                    <div class="text-xs opacity-90 mt-3 truncate" id="ref-link-{{ $user->id }}">{{ $referralLink }}</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="(function(btn){navigator.clipboard.writeText('{{ $referralLink }}'); const t=btn.innerText; btn.innerText='Copied!'; setTimeout(()=>btn.innerText=t,1500);})(this)"
                            class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-semibold backdrop-blur inline-flex items-center gap-1.5 border border-white/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy Link
                    </button>
                    <a href="https://wa.me/?text={{ urlencode('Join via my referral link: ' . $referralLink) }}" target="_blank" rel="noopener"
                       class="bg-white text-brand-crimson hover:bg-white/90 px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.693.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- KPI strip --}}
    <div style="display:flex;gap:0.75rem;">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3 flex-1">
            <div class="w-10 h-10 rounded-lg bg-brand-gold/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Lifetime Earnings</div>
                <div class="text-xl font-bold text-brand-gold leading-tight">&#8377;{{ number_format($totals['lifetime'], 2) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3 flex-1">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Wallet Points</div>
                <div class="text-xl font-bold text-green-600 leading-tight">&#8377;{{ number_format($totals['wallet'], 2) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex items-center gap-3 flex-1">
            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Referrals</div>
                <div class="text-xl font-bold text-text-heading leading-tight">{{ $referredUsersCount }}</div>
            </div>
        </div>
    </div>

    {{-- Tabs nav --}}
    <div class="border-b border-ui-border">
        <nav class="flex gap-1 -mb-px overflow-x-auto" id="aff-tabs" role="tablist">
            <button type="button" data-tab="account" class="aff-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-brand-gold text-brand-gold inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Account Details
            </button>
            <button type="button" data-tab="wallet" class="aff-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                My Wallet
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $walletTransactions->count() }}</span>
            </button>
            <button type="button" data-tab="orders" class="aff-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Orders
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $orderHistory->count() }}</span>
            </button>
            @if($showRefTab)
            <button type="button" data-tab="referrals" class="aff-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Referrals
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $referredUsersCount }}</span>
            </button>
            @endif
            <button type="button" data-tab="lifetime" class="aff-tab-btn px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-text-muted hover:text-text-heading inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Lifetime Earnings
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-gray-100 text-text-muted text-[10px] font-bold">{{ $monthlyEarnings->count() }}</span>
            </button>
        </nav>
    </div>

    {{-- Tab panel: Account Details --}}
    <div data-panel="account" class="aff-tab-panel space-y-6">
    {{-- Your Account --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
            <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <h3 class="font-semibold text-text-heading text-base">Your Account</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Name</div>
                <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $user->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Mobile</div>
                <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $user->mobile ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Email</div>
                <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm truncate">{{ $user->email ?? '—' }}</div>
            </div>
            @if($user->parent)
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Referred By</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $user->parent->name }} <span class="text-text-muted text-xs uppercase">({{ $user->parent->role }})</span></div>
                </div>
            @endif
            <div>
                <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Joined</div>
                <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $user->created_at?->format('d M Y') ?? '—' }}</div>
            </div>
            @if($user->approved_at)
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Approved</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $user->approved_at?->format('d M Y') }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Address --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
            <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <h3 class="font-semibold text-text-heading text-base">Address</h3>
        </div>
        @if($profile && ($profile->address || $profile->city || $profile->state || $profile->pincode))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Address</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $profile->address ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">City</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $profile->city ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">State</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $profile->state ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Pincode</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->pincode ?? '—' }}</div>
                </div>
            </div>
        @else
            <div class="text-center py-6 text-sm text-text-muted">No address submitted</div>
        @endif
    </div>

    {{-- Bank Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <h3 class="font-semibold text-text-heading text-base">Bank Details <span class="text-xs text-text-muted font-normal">(for commission payouts)</span></h3>
            </div>
        </div>
        @if($profile && ($profile->bank_name || $profile->account_number || $profile->upi_id))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Account Holder Name</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $profile->account_holder ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Bank Name</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $profile->bank_name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Account Number</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->account_number ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">IFSC Code</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->ifsc ?? '—' }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">UPI ID <span class="text-text-muted font-normal normal-case">(optional)</span></div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->upi_id ?? '—' }}</div>
                </div>
            </div>
        @else
            <div class="text-center py-6 text-sm text-text-muted">No bank details submitted</div>
        @endif
    </div>

    {{-- KYC Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <h3 class="font-semibold text-text-heading text-base">KYC Details</h3>
            </div>
            @if($profile && $profile->kyc_verified)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    Verified
                </span>
            @endif
        </div>
        @if($profile && ($profile->pan_number || $profile->aadhaar_number))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">PAN Number</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->pan_number ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Aadhaar Number</div>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->aadhaar_number ?? '—' }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">KYC Document <span class="text-text-muted font-normal normal-case">(PAN or Aadhaar copy)</span></div>
                    @if($profile->kyc_doc_path)
                        <a href="{{ asset('storage/' . $profile->kyc_doc_path) }}" target="_blank" class="flex items-center justify-between px-3 py-2.5 bg-brand-gold/5 border border-brand-gold/20 rounded-lg text-sm hover:bg-brand-gold/10 transition">
                            <span class="inline-flex items-center gap-2 text-text-heading font-medium">
                                <svg class="w-4 h-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                KYC document uploaded
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-brand-gold font-semibold">
                                View
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </a>
                    @else
                        <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm text-text-muted">Not uploaded</div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-6 text-sm text-text-muted">No KYC submitted</div>
        @endif
    </div>

    {{-- Review Actions (inside Account Details tab) --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
        <h3 class="font-semibold text-text-heading mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Review Actions
        </h3>

        @if($user->affiliate_status === 'approved')
            <div class="mb-5 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>This affiliate is <strong>approved</strong>@if($user->approved_at) since {{ $user->approved_at->format('d M Y') }}@endif.</span>
            </div>
        @elseif($user->affiliate_status === 'rejected')
            <div class="mb-5 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <strong>This application was rejected.</strong>
                </div>
                @if($profile && $profile->rejection_reason)
                    <div class="mt-1 text-xs">Reason: {{ $profile->rejection_reason }}</div>
                @endif
            </div>
        @endif

        @if($user->affiliate_status !== 'rejected')
            <form method="POST" action="{{ route('admin.affiliates.reject', $user) }}" id="reject-form-{{ $user->id }}">
                @csrf
                <label class="block text-sm font-medium text-text-heading mb-2">
                    Rejection Reason <span class="text-xs text-text-muted font-normal">(only required if you reject &mdash; shown to the user on resubmit)</span>
                </label>
                <input type="text" name="rejection_reason"
                       placeholder="e.g. PAN copy is unclear, please re-upload"
                       class="w-full px-3 py-2.5 border border-ui-border rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 mb-4">
            </form>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @if($user->affiliate_status !== 'approved')
                <form method="POST" action="{{ route('admin.affiliates.approve', $user) }}" class="block w-full m-0">
                    @csrf
                    <button type="submit" style="background-color:#16a34a;color:#ffffff" class="w-full hover:opacity-90 px-4 py-3 rounded-lg text-sm font-bold inline-flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Approve Application
                    </button>
                </form>
            @else
                <button type="button" disabled style="background-color:#dcfce7;color:#15803d" class="w-full px-4 py-3 rounded-lg text-sm font-bold inline-flex items-center justify-center gap-2 cursor-not-allowed opacity-75">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Already Approved
                </button>
            @endif

            @if($user->affiliate_status !== 'rejected')
                <button type="submit" form="reject-form-{{ $user->id }}"
                        onclick="return confirm('Reject this application?')"
                        style="background-color:#dc2626;color:#ffffff"
                        class="w-full hover:opacity-90 px-4 py-3 rounded-lg text-sm font-bold inline-flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject Application
                </button>
            @else
                <button type="button" disabled style="background-color:#fee2e2;color:#b91c1c" class="w-full px-4 py-3 rounded-lg text-sm font-bold inline-flex items-center justify-center gap-2 cursor-not-allowed opacity-75">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    Already Rejected
                </button>
            @endif
        </div>

        @if($profile)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.affiliates.verify-kyc', $user) }}">
                    @csrf
                    <button class="{{ $profile->kyc_verified ? 'text-gray-600 hover:text-gray-800' : 'text-blue-600 hover:text-blue-800' }} text-sm font-semibold inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ $profile->kyc_verified ? 'Remove KYC verification' : 'Mark KYC as verified' }}
                    </button>
                </form>
            </div>
        @endif
    </div>
    </div>

    {{-- Tab panel: My Wallet --}}
    <div data-panel="wallet" class="aff-tab-panel hidden space-y-5">
        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center flex-1" style="min-width:150px;">
                <div class="text-xs text-amber-700 font-bold uppercase tracking-wider mb-1">Lifetime Earnings</div>
                <div class="text-2xl font-bold text-amber-700 tabular-nums">&#8377;{{ number_format($totals['lifetime'], 2) }}</div>
                <div class="text-[10px] text-amber-600 mt-1">Total commissions earned</div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center flex-1" style="min-width:150px;">
                <div class="text-xs text-green-700 font-bold uppercase tracking-wider mb-1">Valid Balance</div>
                <div class="text-2xl font-bold text-green-700 tabular-nums">&#8377;{{ number_format($totals['wallet'], 2) }}</div>
                <div class="text-[10px] text-green-600 mt-1">Available to withdraw</div>
            </div>
        </div>

        @if(auth()->user()->is_admin)
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
            <h3 class="font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Wallet Transaction <span class="text-xs font-normal text-text-muted ml-1">(Admin only)</span>
            </h3>
            <form method="POST" action="{{ route('admin.affiliates.wallet', $user) }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-text-muted mb-1">Type *</label>
                    <select name="type" id="aff_wallet_type" required
                            class="px-3 py-2 border border-ui-border rounded-lg text-sm bg-white min-w-[140px]">
                        <option value="credit">➕ Add (Credit)</option>
                        <option value="debit">➖ Remove (Debit)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-muted mb-1">Amount (₹) *</label>
                    <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0.00"
                           class="px-3 py-2 border border-ui-border rounded-lg text-sm w-36 tabular-nums">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-text-muted mb-1">Remark</label>
                    <input type="text" name="remark" placeholder="Reason (optional)" maxlength="500"
                           class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
                </div>
                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm whitespace-nowrap">
                    Apply
                </button>
            </form>
        </div>
        @endif

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
                                @if(auth()->user()->is_admin)
                                <th class="text-left px-4 py-3 font-semibold">Action</th>
                                @endif
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
                                    @if(auth()->user()->is_admin)
                                    <td class="px-4 py-3">
                                        @if($tx->status === 'pending')
                                            <div class="flex items-center gap-1">
                                                <form method="POST" action="{{ route('admin.affiliates.wallet.approve', $tx) }}" class="inline">
                                                    @csrf
                                                    <button class="px-2 py-1 text-[10px] font-bold bg-green-600 hover:bg-green-700 text-white rounded">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.affiliates.wallet.reject', $tx) }}" class="inline">
                                                    @csrf
                                                    <button class="px-2 py-1 text-[10px] font-bold bg-red-600 hover:bg-red-700 text-white rounded">Reject</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-text-muted text-xs">—</span>
                                        @endif
                                    </td>
                                    @endif
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

    {{-- Tab panel: Orders --}}
    <div data-panel="orders" class="aff-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
                <h3 class="font-semibold text-text-heading flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Order History
                </h3>
                <span class="text-xs text-text-muted">{{ $orderHistory->count() }} order(s)</span>
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
                                <th class="text-left px-4 py-3 font-semibold">Manager</th>
                                <th class="text-right px-4 py-3 font-semibold">Order Total</th>
                                <th class="text-right px-4 py-3 font-semibold">Aff. Earnings</th>
                                <th class="text-left px-4 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($orderHistory as $order)
                                @php
                                    $commMap = $orderCommissionMap[$order->id] ?? [];
                                    $affComm = $commMap['affiliate'] ?? null;
                                    $orderStatusColors = [
                                        'pending'   => 'bg-amber-100 text-amber-700',
                                        'confirmed' => 'bg-blue-100 text-blue-700',
                                        'shipped'   => 'bg-indigo-100 text-indigo-700',
                                        'delivered' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                    $commBadge = [
                                        'pending'  => 'bg-amber-100 text-amber-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'paid'     => 'bg-green-100 text-green-700',
                                        'reversed' => 'bg-gray-200 text-gray-600',
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
                                    <td class="px-4 py-3 text-xs text-text-muted">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-xs text-text-muted">{{ $rm?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-text-muted">{{ $manager?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-semibold text-text-heading">&#8377;{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if($affComm)
                                            <div class="tabular-nums text-xs font-semibold {{ $affComm['status'] === 'reversed' ? 'text-gray-400 line-through' : 'text-purple-700' }}">&#8377;{{ number_format($affComm['amount'], 2) }}</div>
                                        @else
                                            <form method="POST" action="{{ route('admin.orders.backfill-commissions', $order) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-[10px] text-amber-600 hover:text-amber-800 underline font-semibold">Recalculate</button>
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
                    <p class="text-sm text-text-muted">No orders from referred customers yet.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tab panel: Referrals --}}
    @if($showRefTab)
    <div data-panel="referrals" class="aff-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border">
                <h3 class="font-semibold text-text-heading">Referrals ({{ $referredUsersCount }})</h3>
            </div>
            @if($referredUsers->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold">Name</th>
                                <th class="text-left px-4 py-3 font-semibold">Mobile</th>
                                <th class="text-left px-4 py-3 font-semibold">Role</th>
                                <th class="text-left px-4 py-3 font-semibold">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($referredUsers as $r)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-text-heading">{{ $r->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-text-muted">{{ $r->mobile }}</td>
                                    <td class="px-4 py-3"><span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-100 text-gray-600">{{ $r->role }}</span></td>
                                    <td class="px-4 py-3 text-text-muted text-xs">{{ $r->created_at?->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center text-sm text-text-muted">No referrals yet.</div>
            @endif
        </div>
    </div>
    @endif

    {{-- Tab panel: Lifetime Earnings --}}
    <div data-panel="lifetime" class="aff-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
            <div class="px-5 py-4 border-b border-ui-border">
                <h3 class="font-semibold text-text-heading">Monthly Lifetime Earnings</h3>
                <p class="text-xs text-text-muted mt-0.5">Commission earned through referral orders, grouped by month</p>
            </div>
            @if($monthlyEarnings->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold">Month</th>
                                <th class="text-right px-4 py-3 font-semibold">Total Earnings</th>
                                <th class="text-center px-4 py-3 font-semibold">Orders</th>
                                <th class="text-center px-4 py-3 font-semibold">Status</th>
                                <th class="text-center px-4 py-3 font-semibold">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($monthlyEarnings as $m)
                            @php
                                $mLabel   = \Carbon\Carbon::createFromDate($m->year, $m->month, 1)->format('F Y');
                                $mKey     = $m->year . '-' . $m->month;
                                $mDetails = $monthlyCommissionDetails->get($mKey, collect());
                            @endphp
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 font-medium text-text-heading">{{ $mLabel }}</td>
                                <td class="px-4 py-3 text-right font-bold text-brand-gold">&#8377;{{ number_format($m->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center text-text-muted">{{ $m->orders_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($m->month_status === 'paid')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800">&#10003; Paid</span>
                                    @elseif($m->month_status === 'approved')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800">Approved</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="toggleMonthDetail('aff-{{ $mKey }}')" title="View commission details"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 hover:bg-brand-gold/10 text-text-muted hover:text-brand-gold transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr id="aff-{{ $mKey }}" class="hidden bg-amber-50/40">
                                <td colspan="5" class="px-4 py-3">
                                    <div class="text-[10px] font-bold text-text-muted uppercase tracking-wider mb-2">Commission Details — {{ $mLabel }}</div>
                                    @if($mDetails->count())
                                    <div class="overflow-x-auto rounded-lg border border-amber-100">
                                        <table class="w-full text-xs">
                                            <thead class="bg-amber-100/60 text-[10px] uppercase tracking-wider text-amber-800">
                                                <tr>
                                                    <th class="text-left px-3 py-2 font-semibold">Order #</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Date</th>
                                                    <th class="text-right px-3 py-2 font-semibold">Base Amount</th>
                                                    <th class="text-center px-3 py-2 font-semibold">Rate</th>
                                                    <th class="text-right px-3 py-2 font-semibold">Commission</th>
                                                    <th class="text-center px-3 py-2 font-semibold">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-amber-100">
                                                @foreach($mDetails as $cd)
                                                <tr class="hover:bg-amber-50">
                                                    <td class="px-3 py-2 font-mono text-text-heading">{{ $cd->order?->order_number ?? '—' }}</td>
                                                    <td class="px-3 py-2 text-text-muted">{{ $cd->created_at->format('d M Y') }}</td>
                                                    <td class="px-3 py-2 text-right text-text-heading">&#8377;{{ number_format($cd->base_amount, 2) }}</td>
                                                    <td class="px-3 py-2 text-center text-text-muted">{{ number_format($cd->percentage, 1) }}%</td>
                                                    <td class="px-3 py-2 text-right font-semibold text-brand-gold">&#8377;{{ number_format($cd->amount, 2) }}</td>
                                                    <td class="px-3 py-2 text-center">
                                                        @if($cd->status === 'paid')
                                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800">Paid</span>
                                                        @elseif($cd->status === 'approved')
                                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800">Approved</span>
                                                        @else
                                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <p class="text-xs text-text-muted">No order-level details available.</p>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center text-sm text-text-muted">No commission earnings yet.</div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleMonthDetail(id) {
    const row = document.getElementById(id);
    if (row) row.classList.toggle('hidden');
}

(function(){
    const btns = document.querySelectorAll('.aff-tab-btn');
    const panels = document.querySelectorAll('.aff-tab-panel');
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

async function toggleAffSection(btn) {
    const userId  = btn.dataset.user;
    const section = btn.dataset.section;
    const isOn    = btn.dataset.state === 'on';
    const newState = !isOn;
    const colors = { btn: 'bg-brand-gold', border: 'border-orange-200', bg: 'bg-orange-50', icon: 'text-brand-gold' };
    btn.disabled = true;
    try {
        const res = await fetch(`/admin/affiliates/${userId}/toggle-section`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ section, value: newState })
        });
        const json = await res.json();
        if (json.success) {
            btn.dataset.state = newState ? 'on' : 'off';

            // Button color
            if (newState) { btn.classList.remove('bg-gray-300'); btn.classList.add(colors.btn); }
            else          { btn.classList.remove(colors.btn); btn.classList.add('bg-gray-300'); }

            // Thumb position
            const thumb = btn.querySelector('span');
            if (thumb) {
                if (newState) { thumb.classList.remove('translate-x-0'); thumb.classList.add('translate-x-5'); }
                else          { thumb.classList.remove('translate-x-5'); thumb.classList.add('translate-x-0'); }
            }

            // Card background
            const card = btn.closest('[id^="aff-"]');
            if (card) {
                if (newState) {
                    card.classList.remove('border-gray-200', 'bg-gray-50');
                    card.classList.add(colors.border, colors.bg);
                } else {
                    card.classList.remove(colors.border, colors.bg);
                    card.classList.add('border-gray-200', 'bg-gray-50');
                }
                const icon = card.querySelector('svg');
                if (icon) {
                    if (newState) { icon.classList.remove('text-gray-400'); icon.classList.add(colors.icon); }
                    else          { icon.classList.remove(colors.icon); icon.classList.add('text-gray-400'); }
                }
            }
        }
    } catch(e) {}
    btn.disabled = false;
}
</script>
@endsection
