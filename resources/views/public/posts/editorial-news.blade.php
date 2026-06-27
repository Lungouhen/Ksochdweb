{{-- 
    Template 2: Editorial News & Announcements Layout
    Bold, high-priority layout for pressing organization announcements and media updates
--}}
@extends('layouts.app')

@section('content')
<div class="bg-white">
    {{-- Breaking News Banner --}}
    <div class="bg-red-600 text-white px-4 py-3 text-center font-bold uppercase tracking-wider text-sm">
        <span class="inline-block mr-2">📢</span> Latest Announcement
    </div>

    <div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        {{-- Article Header --}}
        <header class="border-b border-gray-200 pb-8 mb-8">
            <h1 class="text-4xl md:text-6xl font-black text-gray-900 leading-tight mb-6">
                {{ $post->title }}
            </h1>
            <p class="text-xl text-gray-600 font-serif italic border-l-4 border-red-600 pl-6 py-2 mb-6">
                {{ $post->excerpt ?: Str::limit(strip_tags($post->body), 200) }}
            </p>
            <div class="flex items-center space-x-6 text-sm font-semibold text-gray-500 uppercase tracking-wide flex-wrap">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $post->published_at->format('F d, Y') }}
                </span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $post->author->name }}
                </span>
                @if($post->category)
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                    {{ $post->category->name }}
                </span>
                @endif
                <span class="flex items-center text-gray-400">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a2 2 0 11-4 0 2 2 0 014 0zm0 0a8 8 0 10-16 0 8 8 0 0016 0z"/>
                    </svg>
                    {{ number_format($post->views_count) }} views
                </span>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            {{-- Main Content Column --}}
            <div class="md:col-span-2">
                @if($post->featured_image)
                <figure class="mb-8">
                    <img src="{{ Storage::url($post->featured_image) }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-auto rounded-lg shadow-md hover:shadow-xl transition duration-300">
                    <figcaption class="text-xs text-gray-400 mt-3 text-right italic">
                        Photo Credit: Organization Archive
                    </figcaption>
                </figure>
                @endif
                
                <div class="prose prose-xl prose-serif max-w-none text-gray-800">
                    {!! $post->body !!}
                </div>

                {{-- Tags Section --}}
                @if($post->tags->count() > 0)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Related Topics</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                                #{{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            {{-- Sidebar Column --}}
            <div class="md:col-span-1">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 sticky top-6">
                    <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm tracking-wide">Key Highlights</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="text-red-600 mr-2 mt-1">✓</span>
                            <span>Official statement released today</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-600 mr-2 mt-1">✓</span>
                            <span>Impact on community members outlined</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-600 mr-2 mt-1">✓</span>
                            <span>Next steps and action items detailed</span>
                        </li>
                    </ul>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h5 class="font-bold text-gray-900 text-xs uppercase mb-2">Media Contact</h5>
                        <p class="text-xs text-gray-500">
                            press@nonprofit.org<br>
                            (555) 123-4567
                        </p>
                    </div>

                    {{-- Share Buttons --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h5 class="font-bold text-gray-900 text-xs uppercase mb-3">Share This</h5>
                        <div class="grid grid-cols-2 gap-2">
                            <button class="bg-blue-600 text-white py-2 rounded text-xs font-semibold hover:bg-blue-700 transition">Facebook</button>
                            <button class="bg-sky-500 text-white py-2 rounded text-xs font-semibold hover:bg-sky-600 transition">Twitter</button>
                            <button class="bg-green-600 text-white py-2 rounded text-xs font-semibold hover:bg-green-700 transition">WhatsApp</button>
                            <button class="bg-gray-700 text-white py-2 rounded text-xs font-semibold hover:bg-gray-800 transition">Copy Link</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Articles Placeholder --}}
        <div class="mt-16 pt-10 border-t border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Related Articles</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-2">Coming Soon</p>
                    <p class="font-semibold text-gray-700">More related articles will appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .prose img {
        @apply rounded-lg shadow-md my-6;
    }
    .prose blockquote {
        @apply border-l-4 border-red-600 pl-4 italic text-gray-700;
    }
</style>
@endpush
