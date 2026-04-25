@extends('layouts.admin')

@section('title', 'Edit Request')

@section('content')
@php
    $req = $withdrawalRequest;
    $isCredit = ($req->request_type ?? 'withdrawal') === 'credit';
    $typeLabel = match($req->request_type ?? 'withdrawal') {
        'credit'    => 'Credit Request',
        'debit'     => 'Debit Request',
        default     => 'Withdrawal',
    };
@endphp

<div class="space-y-6 max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-text-muted">
        <a href="{{ route('admin.withdrawal-requests.index') }}" class="hover:text-brand-gold">Withdrawal Requests</a>
        <span>/</span>
        <a href="{{ route('admin.withdrawal-requests.show', $req) }}" class="hover:text-brand-gold">Request #{{ $req->id }}</a>
        <span>/</span>
        <span class="text-text-heading font-semibold">Edit</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-text-heading">Edit &amp; Approve Request</h1>
        <a href="{{ route('admin.withdrawal-requests.show', $req) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">
            ← Back
        </a>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>@endif

    @if($req->status !== 'pending')
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg text-sm">
            This request has already been <strong>{{ $req->status }}</strong> and cannot be modified.
        </div>
    @endif

    {{-- Request summary --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-6 py-4 border-b border-ui-border bg-gray-50">
            <h2 class="font-semibold text-text-heading">Request Summary</h2>
        </div>
        <div class="divide-y divide-gray-100">
            <div class="flex px-6 py-3">
                <span class="w-36 shrink-0 text-sm text-text-muted">Member</span>
                <span class="text-sm font-semibold text-text-heading">{{ optional($req->user)->name ?? 'User #'.$req->user_id }}
                    <span class="text-text-muted font-normal ml-1">({{ optional($req->user)->mobile }})</span>
                </span>
            </div>
            <div class="flex px-6 py-3">
                <span class="w-36 shrink-0 text-sm text-text-muted">Role</span>
                <span class="text-sm font-bold uppercase text-text-heading">{{ optional($req->user)->role ?? '—' }}</span>
            </div>
            <div class="flex px-6 py-3">
                <span class="w-36 shrink-0 text-sm text-text-muted">Type</span>
                <span class="text-sm font-semibold text-text-heading">{{ $typeLabel }}</span>
            </div>
            <div class="flex px-6 py-3">
                <span class="w-36 shrink-0 text-sm text-text-muted">Amount</span>
                <span class="text-xl font-bold tabular-nums {{ $isCredit ? 'text-green-700' : 'text-amber-700' }}">
                    &#8377;{{ number_format($req->amount, 2) }}
                </span>
            </div>
            @if($req->notes)
                <div class="flex px-6 py-3">
                    <span class="w-36 shrink-0 text-sm text-text-muted">Note</span>
                    <span class="text-sm text-text-heading">{{ $req->notes }}</span>
                </div>
            @endif
            <div class="flex px-6 py-3">
                <span class="w-36 shrink-0 text-sm text-text-muted">Submitted</span>
                <span class="text-sm text-text-muted">{{ $req->created_at?->format('d M Y, h:i A') }}</span>
            </div>
        </div>
    </div>

    @if($req->status === 'pending')
    {{-- Approve --}}
    <div class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-green-100" style="background:#f0fdf4;">
            <h2 class="font-semibold text-green-800 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Approve Request
            </h2>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-text-muted mb-4">
                Approving will {{ $isCredit ? 'credit ₹'.number_format($req->amount,2).' to' : 'debit ₹'.number_format($req->amount,2).' from' }}
                <strong>{{ optional($req->user)->name }}</strong>'s wallet immediately.
            </p>
            <form method="POST" action="{{ route('admin.withdrawal-requests.approve', $req) }}"
                  onsubmit="return confirm('{{ $isCredit ? 'Credit' : 'Approve withdrawal of' }} ₹{{ number_format($req->amount,2) }} for {{ addslashes(optional($req->user)->name) }}?')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm text-white"
                        style="background:linear-gradient(135deg,#16a34a,#15803d);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ $isCredit ? 'Approve & Credit Wallet' : 'Approve & Process Withdrawal' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Reject --}}
    <div id="reject" class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-red-100" style="background:#fff5f5;">
            <h2 class="font-semibold text-red-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Reject Request
            </h2>
        </div>
        <div class="px-6 py-5">
            <form method="POST" action="{{ route('admin.withdrawal-requests.reject', $req) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Reason for Rejection <span class="text-text-muted font-normal">(optional)</span></label>
                    <input type="text" name="rejection_reason" placeholder="Enter reason..." maxlength="500"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm text-white bg-red-600 hover:bg-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    Confirm Reject
                </button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
