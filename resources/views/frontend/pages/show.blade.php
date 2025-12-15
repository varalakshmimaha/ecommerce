@extends('layouts.frontend')

@section('title', $page->title)

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">{{ $page->title }}</h1>
        
        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($page->content)) !!}
        </div>
        
        <div class="mt-12">
            <a href="{{ route('home') }}" class="btn-primary">Back to Home</a>
        </div>
    </div>
</div>
@endsection
