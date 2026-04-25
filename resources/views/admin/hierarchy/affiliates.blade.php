@extends('layouts.admin')

@section('title', 'Affiliates')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-heading">Affiliates</h1>
            <p class="text-text-muted mt-1">Manage applications, KYC review, and referral codes.</p>
        </div>
        <a href="{{ route('admin.affiliates.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2.5 rounded-lg font-semibold shadow hover:shadow-lg transition">
            + Add Affiliate
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap gap-2 mb-4">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
            <a href="{{ route('admin.affiliates.index', ['tab' => $key]) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold {{ $tab === $key ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : 'bg-white border border-ui-border text-text-heading hover:shadow' }}">
                {{ $label }}
                @if(isset($counts[$key]))
                    <span class="ml-1 text-xs {{ $tab === $key ? 'opacity-90' : 'text-text-muted' }}">({{ $counts[$key] }})</span>
                @endif
            </a>
        @endforeach
    </div>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-ui-border p-4 mb-4 flex items-end gap-3">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="flex-1">
            <label class="block text-xs text-text-muted mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, mobile, email, or referral code"
                   class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
        </div>
        <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm">Search</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        @if($affiliates->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-3">User</th>
                            <th class="text-left px-4 py-3">Contact</th>
                            <th class="text-left px-4 py-3">Ref Code</th>
                            <th class="text-left px-4 py-3">Parent</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-right px-4 py-3">Refs</th>
                            <th class="text-right px-4 py-3">Paid</th>
                            <th class="text-left px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($affiliates as $a)
                            @php
                                $profile = $a->affiliateProfile;
                                $t = $commissionTotals[$a->id] ?? [];
                                $paid = $t['paid'] ?? 0;
                                $refs = $referralCounts[$a->id] ?? 0;
                                $st = $a->affiliate_status;
                                $badge = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'none' => 'bg-gray-200 text-gray-600',
                                ][$st] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $a->name ?? '—' }}</div>
                                    <div class="text-xs text-text-muted">ID #{{ $a->id }} · {{ strtoupper($a->role) }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ $a->mobile }}</div>
                                    <div class="text-xs text-text-muted">{{ $a->email ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($a->referral_code)
                                        <code class="font-mono font-bold text-brand-gold text-xs">{{ $a->referral_code }}</code>
                                    @else
                                        <span class="text-text-muted text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($a->parent)
                                        <div class="font-medium">{{ $a->parent->name }}</div>
                                        <div class="text-text-muted uppercase text-[10px]">{{ $a->parent->role }}</div>
                                    @else
                                        <span class="text-text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }} uppercase">{{ $st }}</span>
                                    @if($profile && $profile->kyc_verified)
                                        <div class="mt-1"><span class="inline-block px-2 py-0.5 rounded-full text-[10px] bg-green-100 text-green-700">KYC ✓</span></div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">{{ $refs }}</td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $paid > 0 ? 'text-green-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($paid, 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.affiliates.show', $a) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold">View</a>
                                    <a href="{{ route('admin.affiliates.edit', $a) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold ml-2">Edit</a>
                                    <form method="POST" action="{{ route('admin.affiliates.destroy', $a) }}" class="inline ml-2" onsubmit="return confirm('Delete this affiliate? They will be demoted to customer. Commissions and referrals are preserved.')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-xs font-semibold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-ui-border">{{ $affiliates->links() }}</div>
        @else
            <div class="px-5 py-10 text-center text-text-muted">
                No affiliates in "{{ ucfirst($tab) }}".
            </div>
        @endif
    </div>
</div>
@endsection
