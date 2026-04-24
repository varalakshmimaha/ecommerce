@extends('layouts.frontend')

@section('title', 'Become an Affiliate')

@section('content')
<section class="bg-gradient-to-br from-gray-50 to-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson bg-clip-text text-transparent mb-4">
                Become an Affiliate
            </h1>
            <p class="text-text-muted text-lg mb-10">
                Share products you love. Earn a commission on every sale your referral code drives.
            </p>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 max-w-xl mx-auto">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6 max-w-xl mx-auto">
                    {{ session('info') }}
                </div>
            @endif

            @php
                $hasApplied = auth()->check() && in_array(auth()->user()->affiliate_status, ['pending', 'approved', 'rejected'], true);
            @endphp

            {{-- 3-step explainer: only show BEFORE the user has applied --}}
            @unless($hasApplied)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 text-left">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-brand-gold/10 flex items-center justify-center mb-3">
                            <span class="text-brand-gold font-bold">1</span>
                        </div>
                        <h3 class="font-semibold text-text-heading mb-1">Sign up &amp; apply</h3>
                        <p class="text-sm text-text-muted">Create an account and submit your bank &amp; KYC details.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-brand-gold/10 flex items-center justify-center mb-3">
                            <span class="text-brand-gold font-bold">2</span>
                        </div>
                        <h3 class="font-semibold text-text-heading mb-1">Get approved</h3>
                        <p class="text-sm text-text-muted">Our team reviews your application and issues a referral code.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-brand-gold/10 flex items-center justify-center mb-3">
                            <span class="text-brand-gold font-bold">3</span>
                        </div>
                        <h3 class="font-semibold text-text-heading mb-1">Earn</h3>
                        <p class="text-sm text-text-muted">Share your code. Track pending and paid earnings in your dashboard.</p>
                    </div>
                </div>
            @endunless

            @guest
                <a href="{{ route('user.register') }}?redirect={{ urlencode(route('become.affiliate')) }}"
                   class="inline-block bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Join Now
                </a>
                <p class="text-sm text-text-muted mt-3">
                    Already registered? <a href="{{ route('user.login') }}" class="text-brand-gold font-semibold">Log in</a> to continue your application.
                </p>
            @else
                @unless($hasApplied)
                    <p class="text-text-muted mb-4">You're signed in as <span class="font-semibold text-text-heading">{{ auth()->user()->name ?? auth()->user()->mobile }}</span>.</p>
                @endunless

                @if(auth()->user()->affiliate_status === 'approved' && auth()->user()->role === 'affiliate')
                    <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg mb-6 max-w-xl mx-auto font-semibold">
                        🎉 You're an approved affiliate!
                    </div>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="{{ route('affiliate.dashboard') }}"
                           class="inline-block bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transition">
                            Go to My Earnings
                        </a>
                        <a href="{{ route('user.dashboard') }}"
                           class="inline-flex items-center gap-1.5 bg-white border border-gray-200 text-text-heading px-6 py-3 rounded-lg font-semibold hover:shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Back to Dashboard
                        </a>
                    </div>
                @elseif(auth()->user()->affiliate_status === 'pending')
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-6 py-4 rounded-lg mb-6 max-w-xl mx-auto font-semibold">
                        Your affiliate application is under review.
                    </div>
                    <a href="{{ route('user.dashboard') }}"
                       class="inline-flex items-center gap-1.5 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Dashboard
                    </a>
                @elseif(auth()->user()->affiliate_status === 'rejected')
                    <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg mb-4 max-w-xl mx-auto text-left">
                        <div class="font-semibold">Your previous application was rejected.</div>
                        @if(optional(auth()->user()->affiliateProfile)->rejection_reason)
                            <div class="text-sm mt-1">Reason: {{ auth()->user()->affiliateProfile->rejection_reason }}</div>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="{{ route('become.affiliate.apply.create') }}"
                           class="inline-block bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transition">
                            Resubmit Application
                        </a>
                        <a href="{{ route('user.dashboard') }}"
                           class="inline-flex items-center gap-1.5 bg-white border border-gray-200 text-text-heading px-6 py-3 rounded-lg font-semibold hover:shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Back to Dashboard
                        </a>
                    </div>
                @else
                    <a href="{{ route('become.affiliate.apply.create') }}"
                       class="inline-block bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                        Join Now
                    </a>
                    <p class="text-sm text-text-muted mt-3">You'll fill in your bank &amp; KYC details next.</p>
                @endif
            @endguest
        </div>
    </div>
</section>
@endsection
