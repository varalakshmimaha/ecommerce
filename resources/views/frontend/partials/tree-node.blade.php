@php
    $initial = strtoupper(substr($node->name ?? '?', 0, 1));
    $cardBase = 'flex items-center gap-3 p-3.5 rounded-xl transition-all duration-200';
    $cardClass = $isSelf
        ? $cardBase . ' bg-gradient-to-r from-brand-gold/10 via-brand-amber/10 to-brand-crimson/10 border-2 border-brand-gold shadow-md'
        : $cardBase . ' bg-white border border-gray-200 hover:border-brand-gold/40 hover:shadow-sm';
    $avatarClass = $isSelf
        ? 'w-11 h-11 rounded-full bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md'
        : 'w-11 h-11 rounded-full bg-gradient-to-br from-amber-100 to-amber-200 text-amber-700 border border-amber-300';
    $badgeClass = $isSelf
        ? 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-gold text-white uppercase tracking-wider shadow-sm'
        : 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 uppercase tracking-wider';
@endphp

<div class="{{ $cardClass }}">
    {{-- Avatar with level indicator --}}
    <div class="relative shrink-0">
        <div class="{{ $avatarClass }} flex items-center justify-center font-bold text-base">
            {{ $initial }}
        </div>
        @if(!$isSelf && isset($level))
            <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-white border-2 border-gray-200 flex items-center justify-center text-[10px] font-bold text-text-muted">
                {{ $level }}
            </span>
        @endif
        @if($isSelf)
            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-brand-gold border-2 border-white flex items-center justify-center">
                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>
            </span>
        @endif
    </div>

    {{-- Name + badges --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-semibold text-text-heading truncate">{{ $node->name ?? 'User #'.$node->id }}</span>
            @if($isSelf)
                <span class="text-[11px] font-semibold text-brand-gold bg-brand-gold/10 px-2 py-0.5 rounded-full">you</span>
            @endif
        </div>
        <div class="flex items-center gap-2 mt-1 flex-wrap">
            <span class="{{ $badgeClass }}">Affiliate</span>
            @if($node->referral_code ?? null)
                <code class="text-[11px] font-mono {{ $isSelf ? 'text-brand-gold font-bold' : 'text-text-muted' }}">{{ $node->referral_code }}</code>
            @endif
            @if(isset($meta) && !empty($meta['sub_count']) && $meta['sub_count'] > 0)
                <span class="text-[11px] text-text-muted">· {{ $meta['sub_count'] }} referred</span>
            @endif
        </div>
    </div>

    {{-- Right-side meta --}}
    @if(isset($meta) && !empty($meta['joined']))
        <div class="text-right shrink-0">
            <div class="text-[10px] text-text-muted uppercase tracking-wide">Joined</div>
            <div class="text-xs font-semibold text-text-heading">{{ $meta['joined'] }}</div>
        </div>
    @endif
</div>
