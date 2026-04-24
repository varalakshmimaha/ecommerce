@php
    $isRoot = $level === 'ROOT';
    $isSelf = $isSelf ?? false;

    if ($isSelf) {
        $cardStyle = 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: 2px solid #fbbf24;';
        $textColor = 'color: #ffffff;';
        $codeBg = 'background: rgba(0,0,0,0.18); color: #ffffff;';
    } else {
        $cardStyle = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: 2px solid #34d399;';
        $textColor = 'color: #ffffff;';
        $codeBg = 'background: rgba(0,0,0,0.18); color: #ffffff;';
    }
@endphp

<div class="tree-card" style="position: relative; display: inline-block;">
    {{-- Card body --}}
    <div style="{{ $cardStyle }} border-radius: 10px; padding: 14px 20px 14px 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); min-width: 150px; text-align: center;">
        <div style="{{ $textColor }} font-size: 9px; font-weight: 700; letter-spacing: 1.2px; opacity: 0.85; text-transform: uppercase; margin-bottom: 4px;">
            {{ $isSelf ? 'Affiliate' : 'Referred Affiliate' }}
        </div>
        <div style="{{ $textColor }} font-weight: 700; font-size: 14px; line-height: 1.3; white-space: nowrap; max-width: 180px; overflow: hidden; text-overflow: ellipsis;">
            {{ $node->name ?? 'User #'.$node->id }}
        </div>
        @if($isSelf)
            <div style="{{ $textColor }} font-size: 10px; font-weight: 700; opacity: 0.9; letter-spacing: 1px; margin-top: 2px;">(YOU)</div>
        @endif
        @if(!empty($node->referral_code))
            <div style="{{ $codeBg }} display: inline-block; padding: 3px 10px; border-radius: 6px; margin-top: 6px; font-family: ui-monospace, SFMono-Regular, monospace; font-size: 11px; font-weight: 600;">
                {{ $node->referral_code }}
            </div>
        @endif
    </div>
</div>
