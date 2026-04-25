@extends('layouts.admin')

@section('title', 'Withdrawal Requests')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-text-heading">Withdrawal Requests</h1>
            <p class="text-text-muted mt-1">User-initiated wallet withdrawal requests awaiting approval.</p>
        </div>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>@endif

    {{-- KPI strip --}}
    <div style="display:flex;gap:0.75rem;">
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden flex-1">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#f59e0b;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#fef3c7;">
                    <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#b45309;">Lifetime Total</div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($lifetimeTotal, 2) }}</div>
            <div class="text-[10px] text-text-muted mt-1">All approved payouts</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden flex-1">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#10b981;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#d1fae5;">
                    <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#047857;">Total Wallet Balance</div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">&#8377;{{ number_format($totalWallet, 2) }}</div>
            <div class="text-[10px] text-text-muted mt-1">Across all members</div>
        </div>
        <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden flex-1">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#8b5cf6;"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#ede9fe;">
                    <svg class="w-5 h-5" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#6d28d9;">Total Members</div>
            </div>
            <div class="text-2xl font-bold text-text-heading tabular-nums">{{ $totalMembers }}</div>
            <div class="text-[10px] text-text-muted mt-1">Affiliates, RMs & Managers</div>
        </div>
    </div>

    {{-- Pending requests --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
            <h3 class="font-semibold text-text-heading flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold">{{ $pending->count() }}</span>
                Pending Requests
            </h3>
            <span class="text-xs text-text-muted">Awaiting admin approval</span>
        </div>
        @if($pending->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-amber-50 text-text-muted text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold">Member</th>
                            <th class="text-left px-4 py-3 font-semibold">Role</th>
                            <th class="text-right px-4 py-3 font-semibold">Requested</th>
                            <th class="text-left px-4 py-3 font-semibold">Note</th>
                            <th class="text-left px-4 py-3 font-semibold">Date</th>
                            <th class="text-left px-4 py-3 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pending as $req)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-text-heading">{{ optional($req->user)->name ?? 'User #'.$req->user_id }}</div>
                                    <div class="text-xs text-text-muted">{{ optional($req->user)->mobile }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-100 text-gray-600">{{ optional($req->user)->role ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-bold text-amber-700">&#8377;{{ number_format($req->amount, 2) }}</td>
                                <td class="px-4 py-3 text-xs text-text-muted">{{ $req->notes ?: '—' }}</td>
                                <td class="px-4 py-3 text-xs text-text-muted whitespace-nowrap">{{ $req->created_at?->format('d M Y, h:i A') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.withdrawal-requests.approve', $req) }}" class="inline"
                                              onsubmit="return confirm('Approve ₹{{ number_format($req->amount, 2) }} withdrawal for {{ optional($req->user)->name }}?')">
                                            @csrf
                                            <button type="submit" style="background-color:#16a34a;color:#ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold hover:opacity-90">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Approve
                                            </button>
                                        </form>
                                        <button type="button"
                                            onclick="document.getElementById('reject-form-{{ $req->id }}').classList.toggle('hidden')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold border border-red-200 text-red-600 hover:bg-red-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </div>
                                    <div id="reject-form-{{ $req->id }}" class="hidden mt-2">
                                        <form method="POST" action="{{ route('admin.withdrawal-requests.reject', $req) }}" class="flex gap-2 items-start">
                                            @csrf
                                            <input type="text" name="rejection_reason" placeholder="Reason (optional)" maxlength="500"
                                                   class="px-2 py-1.5 border border-ui-border rounded-lg text-xs flex-1 min-w-[180px]">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-red-600 text-white hover:bg-red-700 whitespace-nowrap">Confirm</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-10 text-center text-sm text-text-muted">No pending withdrawal requests.</div>
        @endif
    </div>

    {{-- Filter + History --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-text-muted mb-1">Search member</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or mobile"
                   class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-text-muted mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-ui-border rounded-lg text-sm bg-white">
                <option value="">All</option>
                @foreach(['pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.withdrawal-requests.index') }}" class="text-sm text-text-muted hover:text-text-heading">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-5 py-4 border-b border-ui-border">
            <h3 class="font-semibold text-text-heading">Request History</h3>
        </div>
        @if($requests->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold">Date</th>
                            <th class="text-left px-4 py-3 font-semibold">Member</th>
                            <th class="text-left px-4 py-3 font-semibold">Role</th>
                            <th class="text-right px-4 py-3 font-semibold">Amount</th>
                            <th class="text-left px-4 py-3 font-semibold">Status</th>
                            <th class="text-left px-4 py-3 font-semibold">Note / Reason</th>
                            <th class="text-left px-4 py-3 font-semibold">Reviewed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requests as $req)
                            @php
                                $badge = [
                                    'pending'  => 'bg-amber-100 text-amber-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ][$req->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 text-xs text-text-muted whitespace-nowrap">{{ $req->created_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-text-heading">{{ optional($req->user)->name ?? 'User #'.$req->user_id }}</div>
                                    <div class="text-xs text-text-muted">{{ optional($req->user)->mobile }}</div>
                                </td>
                                <td class="px-4 py-3 uppercase text-xs font-semibold text-text-muted">{{ optional($req->user)->role ?? '—' }}</td>
                                <td class="px-4 py-3 text-right tabular-nums font-bold {{ $req->status === 'approved' ? 'text-green-700' : 'text-text-heading' }}">&#8377;{{ number_format($req->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $badge }}">{{ $req->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-text-muted">
                                    @if($req->status === 'rejected' && $req->rejection_reason)
                                        <span class="text-red-600">{{ $req->rejection_reason }}</span>
                                    @else
                                        {{ $req->notes ?: '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-text-muted">{{ $req->reviewed_at?->format('d M Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-ui-border">{{ $requests->links() }}</div>
        @else
            <div class="px-5 py-10 text-center text-sm text-text-muted">No withdrawal requests found.</div>
        @endif
    </div>
</div>
@endsection
