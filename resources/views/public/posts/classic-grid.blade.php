{{-- 
    Template 1: Classic Blog Grid Layout
    Clean multi-column grid with search bars, featured tags, and beautiful author/date badges
--}}
@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">{{ $post->title }}</h1>
            <div class="mt-4 flex justify-center items-center space-x-4 text-sm text-gray-500 flex-wrap">
                <span class="flex items-center">
                    <img src="{{ $post->author->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($post->author->name) }}" 
                         class="h-8 w-8 rounded-full mr-2" 
                         alt="{{ $post->author->name }}">
                    {{ $post->author->name }}
                </span>
                <span>•</span>
                <time>{{ $post->published_at->format('F j, Y') }}</time>
                <span>•</span>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold uppercase">
                    {{ $post->category->name ?? 'General' }}
                </span>
            </div>
        </div>

        {{-- Featured Image --}}
        @if($post->featured_image)
        <div class="mb-10 rounded-xl overflow-hidden shadow-lg">
            <img src="{{ Storage::url($post->featured_image) }}" 
                 alt="{{ $post->title }}" 
                 class="w-full h-[400px] object-cover">
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            {{-- Main Content --}}
            <article class="lg:col-span-8 prose prose-lg prose-blue max-w-none">
                {!! $post->body !!}
            </article>

            {{-- Sidebar --}}
            <aside class="lg:col-span-4 space-y-8">
                {{-- Tags Widget --}}
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Related Topics</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($post->tags as $tag)
                            <a href="#" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-md text-sm transition">
                                {{ $tag->name }}
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">No tags assigned.</p>
                        @endforelse
                    </div>
                </div>
                
                {{-- Share Widget --}}
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Share this story</h3>
                    <div class="flex space-x-3">
                        <button class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Facebook</button>
                        <button class="flex-1 bg-sky-500 text-white py-2 rounded hover:bg-sky-600 transition">Twitter</button>
                        <button class="flex-1 bg-blue-700 text-white py-2 rounded hover:bg-blue-800 transition">LinkedIn</button>
                    </div>
                </div>

                {{-- Author Bio --}}
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">About the Author</h3>
                    <div class="flex items-start space-x-3">
                        <img src="{{ $post->author->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($post->author->name) }}" 
                             class="h-12 w-12 rounded-full" 
                             alt="{{ $post->author->name }}">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $post->author->name }}</p>
                            <p class="text-sm text-gray-600">Staff Writer</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        {{-- Comments Section Placeholder --}}
        <div class="mt-12 bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Comments</h3>
            <p class="text-gray-600">Comments section coming soon...</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .prose img {
        @apply rounded-lg shadow-md my-8;
    }
    .prose h2 {
        @apply text-2xl font-bold text-gray-900 mt-8 mb-4;
    }
    .prose h3 {
        @apply text-xl font-semibold text-gray-800 mt-6 mb-3;
    }
    .prose p {
        @apply text-gray-700 leading-relaxed mb-4;
    }
</style>
@endpush
