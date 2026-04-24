@extends('layouts.admin')

@section('title', 'Relationship Managers')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-heading">Relationship Managers</h1>
            <p class="text-text-muted mt-1">Mapped to a Manager. Each RM supervises a group of Affiliates.</p>
        </div>
        <a href="{{ route('admin.rms.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2.5 rounded-lg font-semibold shadow">+ Add RM</a>
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>@endif

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-ui-border p-4 mb-4 flex items-end gap-3">
        <div class="flex-1">
            <label class="block text-xs text-text-muted mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, mobile, or email" class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-text-muted mb-1">Manager</label>
            <select name="manager_id" class="px-3 py-2 border border-ui-border rounded-lg text-sm">
                <option value="">All Managers</option>
                @foreach($managers as $m)
                    <option value="{{ $m->id }}" {{ request('manager_id')==$m->id?'selected':'' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm">Filter</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        @if($rms->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-3">Name</th>
                            <th class="text-left px-4 py-3">Mobile</th>
                            <th class="text-left px-4 py-3">Email</th>
                            <th class="text-left px-4 py-3">Manager</th>
                            <th class="text-right px-4 py-3">Affiliates</th>
                            <th class="text-right px-4 py-3">Pending</th>
                            <th class="text-right px-4 py-3">Approved</th>
                            <th class="text-right px-4 py-3">Paid</th>
                            <th class="text-left px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($rms as $rm)
                            @php
                                $t = $commissionTotals[$rm->id] ?? [];
                                $pending  = $t['pending']  ?? 0;
                                $approved = $t['approved'] ?? 0;
                                $paid     = $t['paid']     ?? 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold">{{ $rm->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $rm->mobile }}</td>
                                <td class="px-4 py-3 text-text-muted">{{ $rm->email ?? '—' }}</td>
                                <td class="px-4 py-3">{{ optional($rm->parent)->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ $rm->affiliates_count }}</td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $pending > 0 ? 'text-amber-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($pending, 0) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $approved > 0 ? 'text-blue-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($approved, 0) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $paid > 0 ? 'text-green-700 font-semibold' : 'text-gray-400' }}">&#8377;{{ number_format($paid, 0) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.rms.show', $rm) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold">View</a>
                                    <a href="{{ route('admin.rms.edit', $rm) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold ml-2">Edit</a>
                                    <form method="POST" action="{{ route('admin.rms.destroy', $rm) }}" class="inline ml-2" onsubmit="return confirm('Remove this RM? Their affiliates and history stay intact.')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-xs font-semibold">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-ui-border">{{ $rms->links() }}</div>
        @else
            <div class="px-5 py-10 text-center text-text-muted">No RMs yet.</div>
        @endif
    </div>
</div>
@endsection
