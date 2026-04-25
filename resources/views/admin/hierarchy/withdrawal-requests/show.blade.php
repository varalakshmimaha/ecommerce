@extends('layouts.admin')

@section('title', 'Request Details')

@section('content')
@php
    $req = $withdrawalRequest;
    $typeLabel = match($req->request_type ?? 'withdrawal') {
        'credit'    => 'Credit Request',
        'debit'     => 'Debit Request',
        default     => 'Withdrawal',
    };
    $statusBg    = match($req->status) { 'approved' => '#dcfce7', 'rejected' => '#fee2e2', default => '#fef9c3' };
    $statusColor = match($req->status) { 'approved' => '#15803d', 'rejected' => '#dc2626', default => '#854d0e' };
@endphp

<div class="space-y-6 max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-text-muted">
        <a href="{{ route('admin.withdrawal-requests.index') }}" class="hover:text-brand-gold">Withdrawal Requests</a>
        <span>/</span>
        <span class="text-text-heading font-semibold">Request #{{ $req->id }}</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-text-heading">Request Details</h1>
        <div class="flex gap-2">
            @if($req->status === 'pending')
                <a href="{{ route('admin.withdrawal-requests.edit', $req) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-amber-200 text-amber-700 hover:bg-amber-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit / Approve
                </a>
            @endif
            <a href="{{ route('admin.withdrawal-requests.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">
                ← Back
            </a>
        </div>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>@endif

    {{-- Detail card --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-6 py-4 border-b border-ui-border bg-gray-50">
            <h2 class="font-semibold text-text-heading">Request #{{ $req->id }}</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @php
                $rows = [
                    ['Member',    optional($req->user)->name ?? 'User #'.$req->user_id],
                    ['Mobile',    optional($req->user)->mobile ?? '—'],
                    ['Role',      strtoupper(optional($req->user)->role ?? '—')],
                    ['Type',      $typeLabel],
                    ['Amount',    '₹'.number_format($req->amount, 2)],
                    ['Note',      $req->notes ?: '—'],
                    ['Submitted', $req->created_at?->format('d M Y, h:i A') ?? '—'],
                ];
            @endphp
            @foreach($rows as [$label, $value])
                <div class="flex px-6 py-4">
                    <span class="w-40 shrink-0 text-sm text-text-muted">{{ $label }}</span>
                    <span class="text-sm font-semibold text-text-heading">{{ $value }}</span>
                </div>
            @endforeach
            <div class="flex px-6 py-4">
                <span class="w-40 shrink-0 text-sm text-text-muted">Status</span>
                <span class="inline-block px-3 py-0.5 rounded-full text-xs font-bold uppercase"
                      style="background:{{ $statusBg }};color:{{ $statusColor }};">{{ $req->status }}</span>
            </div>
            @if($req->status === 'rejected' && $req->rejection_reason)
                <div class="flex px-6 py-4">
                    <span class="w-40 shrink-0 text-sm text-text-muted">Rejection Reason</span>
                    <span class="text-sm text-red-600">{{ $req->rejection_reason }}</span>
                </div>
            @endif
            @if($req->reviewer)
                <div class="flex px-6 py-4">
                    <span class="w-40 shrink-0 text-sm text-text-muted">Reviewed By</span>
                    <span class="text-sm font-semibold text-text-heading">{{ $req->reviewer->name }} on {{ $req->reviewed_at?->format('d M Y, h:i A') }}</span>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
