@extends('layouts.app')

@section('meta_title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 md:p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>

                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! $page->content_html !!}
                </div>

                <div class="mt-8 pt-4 border-t border-gray-200 text-sm text-gray-500">
                    <p>Last updated: {{ $page->updated_at->format('F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection