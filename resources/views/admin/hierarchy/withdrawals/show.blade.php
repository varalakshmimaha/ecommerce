@extends('layouts.admin')

@section('title', 'Withdrawal Receipt')

@section('content')
<div class="space-y-5 max-w-4xl">
    <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-text-muted hover:text-text-heading inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Withdrawals
    </a>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>@endif

    {{-- Receipt card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-6 py-5 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-90">Payout Receipt</div>
                    <div class="font-mono text-lg font-bold mt-1">#WDL-{{ str_pad($withdrawal->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-sm opacity-90 mt-2">{{ $withdrawal->paid_at?->format('d M Y, h:i A') }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-90">Amount Paid</div>
                    <div class="text-3xl font-bold tabular-nums mt-1">&#8377;{{ number_format($withdrawal->amount, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-3">Beneficiary</h3>
                    <div class="space-y-1.5">
                        <div class="font-semibold text-text-heading">{{ optional($withdrawal->user)->name ?? 'User #'.$withdrawal->user_id }}</div>
                        <div class="text-sm text-text-muted">{{ optional($withdrawal->user)->mobile }}</div>
                        <div class="text-sm text-text-muted">{{ optional($withdrawal->user)->email ?? '—' }}</div>
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-brand-gold/10 text-brand-gold mt-1">{{ optional($withdrawal->user)->role ?? '—' }}</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-3">Payment Details</h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between"><dt class="text-text-muted">Method</dt><dd class="font-semibold uppercase">{{ $withdrawal->method }}</dd></div>
                        <div class="flex justify-between"><dt class="text-text-muted">Reference</dt><dd class="font-mono text-xs">{{ $withdrawal->reference ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-text-muted">Recorded By</dt><dd>{{ optional($withdrawal->creator)->name ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-text-muted">Recorded On</dt><dd>{{ $withdrawal->created_at?->format('d M Y h:i A') }}</dd></div>
                    </dl>
                </div>
            </div>

            @if($withdrawal->notes)
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <h3 class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-2">Notes</h3>
                    <p class="text-sm text-text-body whitespace-pre-line">{{ $withdrawal->notes }}</p>
                </div>
            @endif

            @if($profile && in_array($withdrawal->method, ['bank', 'upi'], true))
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <h3 class="text-xs text-text-muted uppercase tracking-wider font-semibold mb-3">Destination Account on File</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><span class="text-text-muted">Account Holder:</span> <strong>{{ $profile->account_holder ?? '—' }}</strong></div>
                        <div><span class="text-text-muted">Bank:</span> <strong>{{ $profile->bank_name ?? '—' }}</strong></div>
                        <div><span class="text-text-muted">A/C No:</span> <span class="font-mono">{{ $profile->account_number ?? '—' }}</span></div>
                        <div><span class="text-text-muted">IFSC:</span> <span class="font-mono">{{ $profile->ifsc ?? '—' }}</span></div>
                        @if($profile->upi_id)<div class="md:col-span-2"><span class="text-text-muted">UPI:</span> <span class="font-mono">{{ $profile->upi_id }}</span></div>@endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Commissions covered --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-5 py-4 border-b border-ui-border">
            <h3 class="font-semibold text-text-heading">Commissions Covered ({{ $withdrawal->commissions->count() }})</h3>
            <p class="text-xs text-text-muted mt-0.5">These commission entries were marked as paid by this withdrawal.</p>
        </div>
        @if($withdrawal->commissions->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-xs uppercase">
                        <tr>
                            <th class="text-left px-4 py-2">Order</th>
                            <th class="text-right px-4 py-2">Amount</th>
                            <th class="text-left px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($withdrawal->commissions as $c)
                            <tr>
                                <td class="px-4 py-2 font-mono text-xs">{{ optional($c->order)->order_number ?? '—' }}</td>
                                <td class="px-4 py-2 text-right tabular-nums font-semibold">&#8377;{{ number_format($c->amount, 2) }}</td>
                                <td class="px-4 py-2"><span class="inline-block px-2 py-0.5 rounded-full text-[10px] uppercase bg-green-100 text-green-700">{{ $c->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-8 text-center text-sm text-text-muted">No commissions were linked to this withdrawal.</div>
        @endif
    </div>

    {{-- Reverse --}}
    <form method="POST" action="{{ route('admin.withdrawals.destroy', $withdrawal) }}" onsubmit="return confirm('Reverse this withdrawal? Linked commissions will return to approved status.')">
        @csrf @method('DELETE')
        <button class="text-red-600 hover:text-red-800 text-sm font-semibold inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            Reverse This Withdrawal
        </button>
    </form>
</div>
@endsection
