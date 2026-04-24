@extends('layouts.admin')

@section('title', 'Commission Settings')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-text-heading">Commission Settings</h1>
        <p class="text-text-muted mt-1">Percentages applied to the order subtotal (before GST and shipping).</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.commission-settings.update') }}" class="bg-white rounded-xl shadow-sm border border-ui-border p-6 space-y-4">
        @csrf
        @method('PUT')

        @foreach(['affiliate' => 'Affiliate', 'rm' => 'Relationship Manager', 'manager' => 'Manager'] as $role => $label)
            @php $row = $rows[$role]; @endphp
            <div class="flex flex-col md:flex-row md:items-center gap-3 border-b border-gray-100 pb-4">
                <div class="flex-1">
                    <div class="font-semibold text-text-heading">{{ $label }}</div>
                    <div class="text-xs text-text-muted uppercase tracking-wide">role: {{ $role }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="number" step="0.01" min="0" max="100"
                           name="settings[{{ $role }}][percentage]"
                           value="{{ old('settings.'.$role.'.percentage', $row->percentage) }}"
                           class="w-28 px-3 py-2 border border-ui-border rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    <span class="text-text-muted">%</span>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-text-heading">
                    <input type="hidden" name="settings[{{ $role }}][is_active]" value="0">
                    <input type="checkbox" name="settings[{{ $role }}][is_active]" value="1" {{ $row->is_active ? 'checked' : '' }}
                           class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    Active
                </label>
            </div>
        @endforeach

        <div class="pt-2">
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2.5 rounded-lg font-semibold hover:shadow-lg transition">
                Save Settings
            </button>
        </div>
    </form>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-900">
        <strong>How commissions apply:</strong>
        <ul class="list-disc list-inside mt-1 space-y-1">
            <li><strong>Referred order</strong> (customer referred by an affiliate): commission splits to Affiliate + RM + Manager based on the chain.</li>
            <li><strong>Self-purchase</strong> by an affiliate/RM/manager: only that user earns their role's percentage. No upline payout.</li>
            <li>Rates are locked in at commission creation time — changing them here does not affect existing commission rows.</li>
        </ul>
    </div>
</div>
@endsection
