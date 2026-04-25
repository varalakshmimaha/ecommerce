@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-text-heading">Create User</h1>
        <p class="text-text-muted mt-1 text-sm">Create an account and assign a role with specific admin permissions.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6 space-y-5">

            {{-- Row 1: Name + Mobile --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-ui-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">
                        Mobile * <span class="text-text-muted font-normal text-xs">(used to log in)</span>
                    </label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" required maxlength="15"
                        class="w-full px-3 py-2 border border-ui-border rounded-lg font-mono focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
            </div>

            {{-- Row 2: Email + Password --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Email <span class="text-text-muted font-normal">(optional)</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-3 py-2 border border-ui-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Password *</label>
                    <input type="text" name="password" value="{{ old('password') }}" required minlength="6"
                        class="w-full px-3 py-2 border border-ui-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    <p class="text-xs text-text-muted mt-1">Share with the user. They can change it after logging in.</p>
                </div>
            </div>

            {{-- Role Name --}}
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Role Name *</label>
                <input type="text" name="role" value="{{ old('role') }}" required placeholder="e.g. admin, manager, supervisor, support…"
                    class="w-full px-4 py-3 rounded-lg text-white font-semibold text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    style="background:#1f2937; border: 1px solid #374151;">
                <p class="text-xs text-text-muted mt-1">Type any role name. Use <strong>admin</strong> to grant full access.</p>
            </div>

            {{-- Permissions --}}
            <div>
                <label class="block text-sm font-medium text-text-heading mb-3">Admin Permissions</label>
                <p class="text-xs text-text-muted mb-3">Choose what sections this user can access in the admin panel. Ignored if role is <strong>admin</strong> (they get full access).</p>
                @php
                    $permList = [
                        'products'            => ['label' => 'Products',             'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',                                                                                                                                                        'color' => '#7c3aed'],
                        'orders'              => ['label' => 'Orders',               'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',                                                                                                                                                                               'color' => '#2563eb'],
                        'customers'           => ['label' => 'Customers',            'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z',                                                                                                                                     'color' => '#059669'],
                        'categories'          => ['label' => 'Categories',           'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z',                                                                                                  'color' => '#d97706'],
                        'banners'             => ['label' => 'Banners',              'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',                                                            'color' => '#0891b2'],
                        'brands'              => ['label' => 'Brands',               'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',                                                                                          'color' => '#be185d'],
                        'product_attributes'  => ['label' => 'Product Attributes',   'icon' => 'M4 6h16M4 12h16M4 18h16',                                                                                                                                                                                                  'color' => '#9333ea'],
                        'pages'               => ['label' => 'Pages',                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                                                                                                  'color' => '#64748b'],
                        'reports'             => ['label' => 'Reports',              'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                                                                                        'color' => '#0f766e'],
                        'settings'            => ['label' => 'Settings',             'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'color' => '#b45309'],
                        'theme_colors'        => ['label' => 'Theme Colors',         'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',                  'color' => '#c026d3'],
                        'payment_methods'     => ['label' => 'Payment Methods',      'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                                                                                                                               'color' => '#0369a1'],
                        'shipping'            => ['label' => 'Shipping',             'icon' => 'M3 7h18M3 12h18M3 17h18',                                                                                                                                                                                                  'color' => '#4f46e5'],
                        'queries'             => ['label' => 'Queries',              'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                              'color' => '#65a30d'],
                        'managers'            => ['label' => 'Managers',             'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',                                                                                                         'color' => '#7c3aed'],
                        'rms'                 => ['label' => 'Relationship Managers', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z',                                                                                                                                   'color' => '#2563eb'],
                        'affiliates'          => ['label' => 'Affiliates',           'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',                                                                                    'color' => '#d97706'],
                        'commissions'         => ['label' => 'Commissions',          'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                        'color' => '#059669'],
                        'commission_settings' => ['label' => 'Commission Settings',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'color' => '#dc2626'],
                        'withdrawal_requests' => ['label' => 'Withdrawal Requests',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',                                                                         'color' => '#0891b2'],
                        'users'               => ['label' => 'Users',                'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',                                                                                                                                  'color' => '#475569'],
                    ];
                    $oldPerms = old('permissions', []);
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($permList as $key => $meta)
                        @php $checked = in_array($key, $oldPerms); @endphp
                        <label class="perm-card flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all select-none"
                               style="border-color: {{ $checked ? $meta['color'] : '#e5e7eb' }}; background: {{ $checked ? 'rgba('.implode(',', sscanf($meta['color'], '#%02x%02x%02x')).',0.06)' : '#fff' }};"
                               onclick="togglePerm(this, '{{ $meta['color'] }}')">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                   class="hidden" {{ $checked ? 'checked' : '' }}>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 style="background: {{ $checked ? $meta['color'] : '#f3f4f6' }};">
                                <svg class="w-4 h-4" fill="none" stroke="{{ $checked ? '#fff' : '#9ca3af' }}" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium leading-tight" style="color: {{ $checked ? $meta['color'] : '#374151' }}">{{ $meta['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button type="submit"
                class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2.5 rounded-lg font-semibold hover:shadow-lg transition-all">
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-lg border border-ui-border text-text-muted text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>

@section('scripts')
<script>
function togglePerm(label, color) {
    const cb = label.querySelector('input[type=checkbox]');
    const icon = label.querySelector('svg');
    const iconWrap = label.querySelector('div');
    const text = label.querySelector('span');
    cb.checked = !cb.checked;
    if (cb.checked) {
        label.style.borderColor = color;
        label.style.background = 'rgba(0,0,0,0.04)';
        iconWrap.style.background = color;
        icon.setAttribute('stroke', '#fff');
        text.style.color = color;
    } else {
        label.style.borderColor = '#e5e7eb';
        label.style.background = '#fff';
        iconWrap.style.background = '#f3f4f6';
        icon.setAttribute('stroke', '#9ca3af');
        text.style.color = '#374151';
    }
}
</script>
@endsection
@endsection
