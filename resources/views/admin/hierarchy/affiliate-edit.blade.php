@extends('layouts.admin')

@section('title', 'Edit Affiliate')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <div class="text-sm text-text-muted"><a href="{{ route('admin.affiliates.show', $user) }}" class="hover:underline">← {{ $user->name ?? 'User #'.$user->id }}</a></div>
        <h1 class="text-3xl font-bold text-text-heading mt-1">Edit Affiliate</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.affiliates.update', $user) }}" class="bg-white rounded-xl shadow-sm border border-ui-border p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Mobile *</label>
                <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" required maxlength="15" class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Password <span class="text-xs text-text-muted">(leave blank to keep)</span></label>
                <input type="text" name="password" minlength="6" class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Referral Code</label>
                <input type="text" name="referral_code" value="{{ old('referral_code', $user->referral_code) }}" maxlength="20" class="w-full px-3 py-2 border border-ui-border rounded-lg font-mono uppercase">
                <p class="text-xs text-text-muted mt-1">Change only if necessary. Existing commissions are unaffected.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Parent (RM or Manager)</label>
                <select name="parent_id" class="w-full px-3 py-2 border border-ui-border rounded-lg">
                    <option value="">— none —</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id', $user->parent_id) == $p->id ? 'selected' : '' }}>
                            [{{ strtoupper($p->role) }}] {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2.5 rounded-lg font-semibold">Save Changes</button>
            <a href="{{ route('admin.affiliates.show', $user) }}" class="text-text-muted hover:text-text-heading text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
