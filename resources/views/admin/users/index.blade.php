@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-heading">Users</h1>
            <p class="text-text-muted mt-1">All accounts across every role.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2.5 rounded-lg font-semibold shadow">
            + Add User
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-ui-border p-4 mb-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-text-muted mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, mobile, or email"
                   class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-text-muted mb-1">Role</label>
            <select name="role" class="px-3 py-2 border border-ui-border rounded-lg text-sm bg-white">
                <option value="">All roles</option>
                <option value="admin"     {{ request('role') === 'admin'     ? 'selected' : '' }}>Admin</option>
                <option value="manager"   {{ request('role') === 'manager'   ? 'selected' : '' }}>Manager</option>
                <option value="rm"        {{ request('role') === 'rm'        ? 'selected' : '' }}>RM</option>
                <option value="affiliate" {{ request('role') === 'affiliate' ? 'selected' : '' }}>Affiliate</option>
                <option value="customer"  {{ request('role') === 'customer'  ? 'selected' : '' }}>Customer</option>
            </select>
        </div>
        <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm">Search</button>
        @if(request()->hasAny(['search','role']))
            <a href="{{ route('admin.users.index') }}" class="text-sm text-text-muted hover:text-text-heading">Clear</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        @if($users->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-4 py-3">Name</th>
                            <th class="text-left px-4 py-3">Mobile</th>
                            <th class="text-left px-4 py-3">Email</th>
                            <th class="text-left px-4 py-3">Role</th>
                            <th class="text-left px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $u)
                            @php
                                $roleColors = [
                                    'admin'     => 'bg-red-100 text-red-700',
                                    'manager'   => 'bg-amber-100 text-amber-700',
                                    'rm'        => 'bg-indigo-100 text-indigo-700',
                                    'affiliate' => 'bg-purple-100 text-purple-700',
                                    'customer'  => 'bg-gray-100 text-gray-600',
                                ];
                                $roleClass = $roleColors[$u->role] ?? 'bg-gray-100 text-gray-600';
                                $routePrefix = match($u->role) {
                                    'manager'   => 'managers',
                                    'rm'        => 'rms',
                                    'affiliate' => 'affiliates',
                                    'customer'  => 'customers',
                                    default     => null,
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold text-text-heading">{{ $u->name ?? '—' }}</td>
                                <td class="px-4 py-3 font-mono text-text-muted">{{ $u->mobile }}</td>
                                <td class="px-4 py-3 text-text-muted">{{ $u->email ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $roleClass }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        @if($routePrefix)
                                            <a href="{{ route('admin.' . $routePrefix . '.show', $u) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                View
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.users.edit', $u) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                              class="inline" onsubmit="return confirm('Delete {{ addslashes($u->name ?? 'this user') }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-red-50 text-red-700 hover:bg-red-100 border border-red-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-ui-border">{{ $users->links() }}</div>
        @else
            <div class="px-5 py-10 text-center text-text-muted">No users found.</div>
        @endif
    </div>
</div>
@endsection
