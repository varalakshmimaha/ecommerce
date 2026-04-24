@extends('layouts.frontend')

@section('title', 'My Team')

@section('content')
<section class="bg-gradient-to-br from-gray-50 to-white py-10 min-h-screen">
    <div class="container mx-auto px-4">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-text-muted hover:text-brand-gold transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-text-heading">My Team</h1>
                <p class="text-text-muted mt-1 text-sm">
                    Signed in as <span class="font-semibold">{{ $user->name ?? $user->mobile }}</span>
                    <span class="ml-2 inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-brand-gold/10 text-brand-gold uppercase">{{ $user->role }}</span>
                </p>
            </div>
            <a href="{{ route('affiliate.dashboard') }}" class="text-sm text-brand-gold hover:underline">My Earnings →</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-text-heading mb-1">Add {{ $childRole === 'rm' ? 'Relationship Manager' : 'Affiliate' }}</h2>
                    <p class="text-xs text-text-muted mb-4">They'll be added directly under you.</p>

                    <form method="POST" action="{{ route('team.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Mobile *</label>
                            <input type="text" name="mobile" value="{{ old('mobile') }}" required maxlength="15" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Temporary Password *</label>
                            <input type="text" name="password" value="{{ old('password') }}" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-text-muted mt-1">Share with them; they can change it later.</p>
                        </div>
                        <button class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2.5 rounded-lg font-semibold">
                            Create {{ $childRole === 'rm' ? 'RM' : 'Affiliate' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-semibold text-text-heading">{{ $childRoleLabel }} ({{ $team->total() }})</h2>
                    </div>
                    @if($team->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                                    <tr>
                                        <th class="text-left px-4 py-3">Name</th>
                                        <th class="text-left px-4 py-3">Mobile</th>
                                        @if($childRole === 'affiliate')
                                            <th class="text-left px-4 py-3">Referral Code</th>
                                        @else
                                            <th class="text-right px-4 py-3">Affiliates</th>
                                        @endif
                                        <th class="text-left px-4 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($team as $m)
                                        <tr>
                                            <td class="px-4 py-3 font-semibold">{{ $m->name }}</td>
                                            <td class="px-4 py-3">{{ $m->mobile }}</td>
                                            @if($childRole === 'affiliate')
                                                <td class="px-4 py-3">
                                                    @if($m->referral_code)
                                                        <code class="font-mono font-bold text-brand-gold">{{ $m->referral_code }}</code>
                                                    @else
                                                        <span class="text-text-muted text-xs">—</span>
                                                    @endif
                                                </td>
                                            @else
                                                <td class="px-4 py-3 text-right">{{ $m->referrals_count }}</td>
                                            @endif
                                            <td class="px-4 py-3">
                                                <form method="POST" action="{{ route('team.destroy', $m) }}" class="inline" onsubmit="return confirm('Remove this team member?')">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-600 hover:text-red-800 text-xs font-semibold">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100">{{ $team->links() }}</div>
                    @else
                        <div class="px-5 py-10 text-center text-text-muted">No team members yet. Add your first {{ $childRole === 'rm' ? 'RM' : 'affiliate' }} using the form.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
