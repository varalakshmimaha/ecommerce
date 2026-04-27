@extends('layouts.admin')

@section('title', 'Withdrawal Request #{{ $withdrawalRequest->id }}')

@section('content')
@php
    $req = $withdrawalRequest;
    $isCredit   = ($req->request_type ?? 'withdrawal') === 'credit';
    $typeLabel  = match($req->request_type ?? 'withdrawal') {
        'credit' => 'Credit Request',
        'debit'  => 'Debit Request',
        default  => 'Withdrawal',
    };
    $isPending  = $req->status === 'pending';
    $statusColor = match($req->status) {
        'approved' => ['bg' => '#dcfce7', 'text' => '#15803d', 'border' => '#bbf7d0'],
        'rejected' => ['bg' => '#fee2e2', 'text' => '#dc2626', 'border' => '#fecaca'],
        default    => ['bg' => '#fef9c3', 'text' => '#854d0e', 'border' => '#fde047'],
    };
    $userName   = optional($req->user)->name ?? 'User #'.$req->user_id;
    $userMobile = optional($req->user)->mobile ?? '—';
    $userRole   = strtoupper(optional($req->user)->role ?? '—');
    $initial    = strtoupper(mb_substr($userName, 0, 1));
@endphp

<div class="max-w-3xl space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-text-muted">
        <a href="{{ route('admin.withdrawal-requests.index') }}" class="hover:text-brand-gold transition-colors">Withdrawal Requests</a>
        <svg class="w-3.5 h-3.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.withdrawal-requests.show', $req) }}" class="hover:text-brand-gold transition-colors">Request #{{ $req->id }}</a>
        <svg class="w-3.5 h-3.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-text-heading">Review</span>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Hero card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-ui-border overflow-hidden">
        {{-- Gradient banner --}}
        <div class="h-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson"></div>

        <div class="px-6 pt-6 pb-4 flex flex-wrap items-start justify-between gap-4">
            {{-- Left: member info --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson flex items-center justify-center text-white text-xl font-bold shadow-md shrink-0">
                    {{ $initial }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-bold text-text-heading">{{ $userName }}</h2>
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-brand-gold/10 text-brand-gold tracking-wider">{{ $userRole }}</span>
                    </div>
                    <div class="text-sm text-text-muted mt-0.5">{{ $userMobile }}</div>
                    <div class="text-xs text-text-muted mt-1">Submitted {{ $req->created_at?->format('d M Y, h:i A') }}</div>
                </div>
            </div>
            {{-- Right: status + back --}}
            <div class="flex flex-col items-end gap-3">
                <a href="{{ route('admin.withdrawal-requests.show', $req) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider"
                      style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};border:1px solid {{ $statusColor['border'] }};">
                    @if($req->status === 'approved')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @elseif($req->status === 'rejected')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                    {{ ucfirst($req->status) }}
                </span>
            </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-ui-border mx-6"></div>

        {{-- Amount + type strip --}}
        <div class="px-6 py-5 flex flex-wrap items-center gap-6">
            <div class="flex-1 min-w-[160px]">
                <div class="text-[11px] uppercase tracking-wider font-bold text-text-muted mb-1">Request Type</div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:{{ $isCredit ? '#dcfce7' : '#fff7ed' }};">
                        @if($isCredit)
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        @else
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        @endif
                    </div>
                    <span class="font-bold text-text-heading">{{ $typeLabel }}</span>
                </div>
            </div>
            <div class="flex-1 min-w-[160px]">
                <div class="text-[11px] uppercase tracking-wider font-bold text-text-muted mb-1">Amount</div>
                <div class="text-3xl font-extrabold tabular-nums {{ $isCredit ? 'text-green-600' : 'text-amber-600' }}">
                    &#8377;{{ number_format($req->amount, 2) }}
                </div>
            </div>
            @if($req->notes)
            <div class="flex-1 min-w-[160px]">
                <div class="text-[11px] uppercase tracking-wider font-bold text-text-muted mb-1">Note from Member</div>
                <div class="text-sm font-medium text-text-heading bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">{{ $req->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    @if(!$isPending)
        {{-- Already actioned banner --}}
        <div class="flex items-center gap-3 px-5 py-4 rounded-xl border text-sm font-medium"
             style="background:{{ $statusColor['bg'] }};border-color:{{ $statusColor['border'] }};color:{{ $statusColor['text'] }};">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($req->status === 'approved')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
            This request has already been <strong class="mx-1">{{ $req->status }}</strong> and cannot be modified.
        </div>
    @else
        {{-- Action cards side by side on md+ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Approve card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-green-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-green-100 flex items-center gap-3" style="background:#f0fdf4;">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="font-bold text-green-800 text-sm">Approve Request</h3>
                </div>
                <div class="px-5 py-5">
                    <p class="text-sm text-text-muted mb-5 leading-relaxed">
                        Approving will immediately
                        <span class="font-semibold {{ $isCredit ? 'text-green-700' : 'text-amber-700' }}">
                            {{ $isCredit ? 'credit' : 'debit' }} &#8377;{{ number_format($req->amount, 2) }}
                        </span>
                        {{ $isCredit ? 'to' : 'from' }} <strong>{{ $userName }}</strong>'s wallet.
                    </p>
                    <form method="POST" action="{{ route('admin.withdrawal-requests.approve', $req) }}"
                          onsubmit="return confirm('{{ $isCredit ? 'Credit' : 'Approve withdrawal of' }} ₹{{ number_format($req->amount,2) }} for {{ addslashes($userName) }}?')">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-bold text-sm text-white transition-all hover:shadow-lg hover:-translate-y-0.5"
                                style="background:linear-gradient(135deg,#16a34a,#15803d);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $isCredit ? 'Approve & Credit Wallet' : 'Approve & Process Withdrawal' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Reject card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-red-100 flex items-center gap-3" style="background:#fff5f5;">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="font-bold text-red-700 text-sm">Reject Request</h3>
                </div>
                <div class="px-5 py-5">
                    <form method="POST" action="{{ route('admin.withdrawal-requests.reject', $req) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-text-muted mb-2">
                                Reason <span class="normal-case font-normal">(optional)</span>
                            </label>
                            <input type="text" name="rejection_reason" placeholder="e.g. Insufficient verification…" maxlength="500"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition">
                        </div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-bold text-sm text-white bg-red-600 hover:bg-red-700 transition-all hover:shadow-lg hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Confirm Reject
                        </button>
                    </form>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
