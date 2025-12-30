@extends('layouts.frontend')

@section('title', $page->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-fade-in">
                <div class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson px-8 py-6">
                    <h1 class="text-4xl font-bold text-white">{{ $page->title }}</h1>
                </div>

                <div class="p-8">
                    <div class="prose prose-lg prose-invert max-w-none leading-relaxed">
                        <div class="text-text-muted space-y-4">
                            {!! $page->content !!}
                        </div>
                    </div>

                    <div class="mt-12 pt-6 border-t border-gray-100">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg hover:shadow-brand-gold/50 transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}
</style>
@endsection
