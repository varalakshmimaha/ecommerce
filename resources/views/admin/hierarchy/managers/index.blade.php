@extends('layouts.admin')

@section('title', 'Managers')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-heading">Managers</h1>
            <p class="text-text-muted mt-1">Top-tier of the hierarchy. Managers supervise RMs.</p>
        </div>
        <a href="{{ route('admin.managers.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2.5 rounded-lg font-semibold shadow">+ Add Manager</a>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>@endif

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-ui-border p-4 mb-4 flex items-end gap-3">
        <div class="flex-1">
            <label class="block text-xs text-text-muted mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, mobile, or email"
                   class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
        </div>
        <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm">Search</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        @if($managers->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-3">Name</th>
                            <th class="text-left px-4 py-3">Mobile</th>
                            <th class="text-left px-4 py-3">Email</th>
                            <th class="text-right px-4 py-3">RMs</th>
                            <th class="text-right px-4 py-3">Affiliates</th>
                            <th class="text-right px-4 py-3">Commissions</th>
                            <th class="text-right px-4 py-3">Pending</th>
                            <th class="text-right px-4 py-3">Approved</th>
                            <th class="text-right px-4 py-3">Paid</th>
                            <th class="text-left px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($managers as $m)
                            @php
                                $t = $commissionTotals[$m->id] ?? [];
                                $pending  = $t['pending']  ?? 0;
                                $approved = $t['approved'] ?? 0;
                                $paid     = $t['paid']     ?? 0;
                                $affCount = $affiliatesCounts[$m->id] ?? 0;
                                $commCount = $commissionCounts[$m->id] ?? 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold">{{ $m->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $m->mobile }}</td>
                                <td class="px-4 py-3 text-text-muted">{{ $m->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">{{ $m->rms_count }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-xs font-bold">{{ $affCount }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full bg-gray-100 text-text-heading text-xs font-bold">{{ $commCount }}</span>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $pending > 0 ? 'text-amber-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($pending, 0) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $approved > 0 ? 'text-blue-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($approved, 0) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $paid > 0 ? 'text-green-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($paid, 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.managers.show', $m) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold">View</a>
                                    <a href="{{ route('admin.managers.edit', $m) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold ml-2">Edit</a>
                                    <form method="POST" action="{{ route('admin.managers.destroy', $m) }}" class="inline ml-2" onsubmit="return confirm('Remove this manager? Their RMs and history stay intact.')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-xs font-semibold">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-ui-border">{{ $managers->links() }}</div>
        @else
            <div class="px-5 py-10 text-center text-text-muted">No managers yet.</div>
        @endif
    </div>
</div>
@endsection
