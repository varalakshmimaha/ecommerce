@extends('layouts.admin')

@section('title', 'Add Affiliate')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-text-heading">Add Affiliate</h1>
        <p class="text-text-muted mt-1">Create an approved affiliate directly. A referral code will be auto-generated.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.affiliates.store') }}" class="bg-white rounded-xl shadow-sm border border-ui-border p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Mobile *</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}" required maxlength="15"
                       class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Temporary Password *</label>
                <input type="text" name="password" value="{{ old('password') }}" required minlength="6"
                       class="w-full px-3 py-2 border border-ui-border rounded-lg">
                <p class="text-xs text-text-muted mt-1">Share this with the affiliate. They can change it later.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Manager</label>
                <select name="manager_id" id="manager-select" class="w-full px-3 py-2 border border-ui-border rounded-lg">
                    <option value="">— none —</option>
                    @foreach($managers as $m)
                        <option value="{{ $m->id }}" {{ old('manager_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Relationship Manager</label>
                <select name="rm_id" id="rm-select" class="w-full px-3 py-2 border border-ui-border rounded-lg">
                    <option value="">— none —</option>
                    @foreach($rms as $r)
                        <option value="{{ $r->id }}" data-manager="{{ $r->parent_id }}" {{ old('rm_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-text-muted mt-1">RM becomes the direct parent. If no RM is selected, the Manager becomes the parent.</p>
            </div>
        </div>

        <script>
            (function () {
                const managerSel = document.getElementById('manager-select');
                const rmSel = document.getElementById('rm-select');
                if (!managerSel || !rmSel) return;

                const allRmOptions = Array.from(rmSel.querySelectorAll('option[value]')).filter(o => o.value !== '');

                function filterRms() {
                    const mid = managerSel.value;
                    const currentRm = rmSel.value;

                    allRmOptions.forEach(opt => {
                        const managerOfRm = opt.getAttribute('data-manager') || '';
                        const show = !mid || managerOfRm === mid;
                        opt.hidden = !show;
                        opt.disabled = !show;
                    });

                    // If currently selected RM is hidden after filter, reset
                    const currentOpt = allRmOptions.find(o => o.value === currentRm);
                    if (currentOpt && currentOpt.hidden) {
                        rmSel.value = '';
                    }
                }

                managerSel.addEventListener('change', filterRms);
                filterRms();
            })();
        </script>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2.5 rounded-lg font-semibold">Create Affiliate</button>
            <a href="{{ route('admin.affiliates.index') }}" class="text-text-muted hover:text-text-heading text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
