@extends('layouts.admin')
@section('title', 'Lifetime Earnings Paid')

@section('content')
<div class="space-y-6">

    {{-- Header KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Paid</div>
                <div class="text-2xl font-bold text-green-600">&#8377;{{ number_format($totalPaid, 2) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Pending Payout</div>
                <div class="text-2xl font-bold text-amber-600">&#8377;{{ number_format($totalPending, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="admin-card">
        <form method="GET" action="{{ route('admin.paid-earnings.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Role</label>
                <select name="role" class="input-field py-2 text-sm">
                    <option value="">All Roles</option>
                    <option value="affiliate" {{ $filterRole === 'affiliate' ? 'selected' : '' }}>Affiliate</option>
                    <option value="rm"        {{ $filterRole === 'rm'        ? 'selected' : '' }}>RM</option>
                    <option value="manager"   {{ $filterRole === 'manager'   ? 'selected' : '' }}>Manager</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Month</label>
                <input type="month" name="month" value="{{ $filterMonth }}" class="input-field py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="input-field py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="paid"     {{ $filterStatus === 'paid'     ? 'selected' : '' }}>Paid</option>
                    <option value="approved" {{ $filterStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending"  {{ $filterStatus === 'pending'  ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg text-sm font-semibold">Filter</button>
            <a href="{{ route('admin.paid-earnings.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200">Reset</a>
        </form>
    </div>

    {{-- Monthly Earnings Table --}}
    <div class="admin-card">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Monthly Lifetime Earnings</h2>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="text-left">Name</th>
                        <th class="text-left">Role</th>
                        <th class="text-left">Month</th>
                        <th class="text-right">Total Earnings</th>
                        <th class="text-center">Commissions</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    @php
                        $monthLabel = \Carbon\Carbon::createFromDate($row->year, $row->month, 1)->format('F Y');
                        $userName   = $userNames[$row->beneficiary_user_id] ?? 'Unknown';
                    @endphp
                    <tr>
                        <td class="font-medium text-gray-900">{{ $userName }}</td>
                        <td>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                {{ $row->beneficiary_role === 'affiliate' ? 'bg-purple-100 text-purple-800' : ($row->beneficiary_role === 'rm' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ ucfirst($row->beneficiary_role) }}
                            </span>
                        </td>
                        <td class="font-medium">{{ $monthLabel }}</td>
                        <td class="text-right font-bold text-gray-900">&#8377;{{ number_format($row->total_amount, 2) }}</td>
                        <td class="text-center text-gray-600">{{ $row->commission_count }}</td>
                        <td class="text-center">
                            @if($row->month_status === 'paid')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">&#10003; Paid</span>
                            @elseif($row->month_status === 'approved')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Approved</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row->month_status !== 'paid')
                            <form action="{{ route('admin.paid-earnings.mark-paid') }}" method="POST"
                                  onsubmit="return confirm('Mark {{ $userName }}\'s {{ $monthLabel }} earnings (₹{{ number_format($row->total_amount,2) }}) as paid?')">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $row->beneficiary_user_id }}">
                                <input type="hidden" name="year"    value="{{ $row->year }}">
                                <input type="hidden" name="month"   value="{{ $row->month }}">
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white hover:shadow-md transition-all">
                                    Mark Paid
                                </button>
                            </form>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">No earnings records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
