@extends('layouts.admin')

@section('title', 'Record Withdrawal')

@section('content')
<div class="space-y-5">
    <div>
        <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-text-muted hover:text-text-heading inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Withdrawals
        </a>
        <h1 class="text-3xl font-bold text-text-heading mt-2">Record Withdrawal</h1>
        <p class="text-text-muted mt-1">Pay out approved commissions to a specific beneficiary.</p>
    </div>

    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.withdrawals.store') }}" class="space-y-5">
        @csrf

        {{-- Beneficiary picker --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
            <h3 class="font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Beneficiary
            </h3>
            <label class="block text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Select User <span class="text-red-500">*</span></label>
            <select name="user_id" onchange="window.location.href='{{ route('admin.withdrawals.create') }}?user_id=' + this.value" class="w-full px-3 py-2.5 border border-ui-border rounded-lg text-sm" required>
                <option value="">— choose a beneficiary —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ optional($user)->id == $u->id ? 'selected' : '' }}>
                        [{{ strtoupper($u->role) }}] {{ $u->name }} · {{ $u->mobile }}
                    </option>
                @endforeach
            </select>
        </div>

        @if($user)
            {{-- Balance summary --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#f59e0b;"></div>
                    <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#b45309;">Pending</div>
                    <div class="text-2xl font-bold text-text-heading tabular-nums mt-1">&#8377;{{ number_format($pendingTotal, 2) }}</div>
                    <div class="text-xs text-text-muted mt-1">Not yet approved — can't be paid</div>
                </div>
                <div class="relative bg-white rounded-xl shadow-sm border-2 p-5 overflow-hidden" style="border-color:#3b82f6;">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#3b82f6;"></div>
                    <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#1d4ed8;">Approved Balance</div>
                    <div class="text-3xl font-bold tabular-nums mt-1" style="color:#1d4ed8;">&#8377;{{ number_format($approvedBalance, 2) }}</div>
                    <div class="text-xs text-text-muted mt-1">Max withdrawable amount</div>
                </div>
                <div class="relative bg-white rounded-xl shadow-sm border border-ui-border p-5 overflow-hidden">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#10b981;"></div>
                    <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#047857;">Lifetime Paid</div>
                    <div class="text-2xl font-bold text-text-heading tabular-nums mt-1">&#8377;{{ number_format($paidLifetime, 2) }}</div>
                    <div class="text-xs text-text-muted mt-1">Already settled</div>
                </div>
            </div>

            {{-- Payment details --}}
            <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
                <h3 class="font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Payment Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Amount <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-muted">&#8377;</span>
                            <input type="number" name="amount" step="0.01" min="0.01" max="{{ $approvedBalance }}" value="{{ old('amount', $approvedBalance) }}" class="w-full pl-7 pr-3 py-2.5 border border-ui-border rounded-lg text-sm font-semibold" required>
                        </div>
                        <p class="text-xs text-text-muted mt-1">Max: &#8377;{{ number_format($approvedBalance, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Paid On</label>
                        <input type="date" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d')) }}" class="w-full px-3 py-2.5 border border-ui-border rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Method <span class="text-red-500">*</span></label>
                        <select name="method" class="w-full px-3 py-2.5 border border-ui-border rounded-lg text-sm" required>
                            <option value="bank" {{ old('method')==='bank'?'selected':'' }}>Bank Transfer</option>
                            <option value="upi" {{ old('method')==='upi'?'selected':'' }}>UPI</option>
                            <option value="cash" {{ old('method')==='cash'?'selected':'' }}>Cash</option>
                            <option value="other" {{ old('method')==='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Reference <span class="text-text-muted font-normal normal-case">(UTR / UPI txn ID)</span></label>
                        <input type="text" name="reference" value="{{ old('reference') }}" placeholder="e.g. UTR123456789" class="w-full px-3 py-2.5 border border-ui-border rounded-lg text-sm font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Notes <span class="text-text-muted font-normal normal-case">(optional)</span></label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2.5 border border-ui-border rounded-lg text-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Beneficiary payment account --}}
            @if($profile)
                <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
                    <h3 class="font-semibold text-text-heading mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Beneficiary's Payment Account
                        <span class="text-xs text-text-muted font-normal">(use these details to send the transfer)</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Account Holder</div>
                            <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $profile->account_holder ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Bank Name</div>
                            <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm">{{ $profile->bank_name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">Account Number</div>
                            <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->account_number ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">IFSC Code</div>
                            <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->ifsc ?? '—' }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-1">UPI ID</div>
                            <div class="px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-mono">{{ $profile->upi_id ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-sm">
                    <strong>Note:</strong> No bank / UPI profile submitted by this user. Use a side channel to confirm the payment destination.
                </div>
            @endif

            {{-- Approved commissions list --}}
            @if($approvedCommissions->count())
                <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
                    <div class="px-5 py-4 border-b border-ui-border">
                        <h3 class="font-semibold text-text-heading">Approved Commissions Being Paid</h3>
                        <p class="text-xs text-text-muted mt-1">Starting from oldest, commissions up to the amount above will be marked as paid.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                                <tr>
                                    <th class="text-left px-4 py-2">Order</th>
                                    <th class="text-right px-4 py-2">Amount</th>
                                    <th class="text-left px-4 py-2">Approved</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($approvedCommissions as $c)
                                    <tr>
                                        <td class="px-4 py-2 font-mono text-xs">{{ optional($c->order)->order_number ?? '—' }}</td>
                                        <td class="px-4 py-2 text-right tabular-nums font-semibold">&#8377;{{ number_format($c->amount, 2) }}</td>
                                        <td class="px-4 py-2 text-text-muted text-xs">{{ $c->created_at?->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.withdrawals.index') }}" class="px-5 py-2.5 text-sm font-semibold text-text-muted hover:text-text-heading">Cancel</a>
                <button type="submit" style="background-color:#16a34a;color:#ffffff;" class="px-6 py-2.5 rounded-lg text-sm font-bold hover:opacity-90 inline-flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Record Withdrawal
                </button>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-ui-border p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <p class="text-sm text-text-muted">Select a beneficiary above to see their balance and payment details.</p>
            </div>
        @endif
    </form>
</div>
@endsection
