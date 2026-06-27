{{-- 
    Template 5: Minimalist Legal & Information Layout
    Clean, distraction-free typography for terms, privacy policies, and text-heavy pages
--}}
@extends('layouts.app')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Simple Header --}}
        <div class="border-b border-gray-200 pb-8 mb-8">
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-2">
                {{ $page->title }}
            </h1>
            <p class="text-sm text-gray-500 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Last updated: {{ $page->updated_at->format('F j, Y') }}
            </p>
        </div>

        {{-- Clean Typography Content --}}
        <div class="prose prose-slate prose-lg max-w-none text-gray-600">
            {!! $page->body !!}
        </div>

        {{-- Table of Contents (if needed) --}}
        @php
            // Extract headings from content for TOC
            preg_match_all('/<h[23]>(.*?)<\/h[23]>/s', $page->body, $matches);
        @endphp
        
        @if(count($matches[0]) > 2)
        <div class="mt-12 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Table of Contents
            </h3>
            <ul class="space-y-2 text-sm">
                @foreach($matches[0] as $index => $heading)
                    @php
                        $slug = Str::slug(strip_tags($heading));
                        $level = strpos($heading, '<h2>') !== false ? 'font-semibold' : 'ml-4';
                    @endphp
                    <li>
                        <a href="#{{ $slug }}" class="text-blue-600 hover:text-blue-800 hover:underline {{ $level }}">
                            {!! strip_tags($heading, '<strong><em>') !!}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Contact/Support Section --}}
        <div class="mt-16 pt-8 border-t border-gray-200">
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Questions or Concerns?</h3>
                <p class="text-gray-600 mb-4">
                    If you have any questions about this policy or need assistance, please don't hesitate to contact us.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="mailto:legal@nonprofit.org" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        legal@nonprofit.org
                    </a>
                    <a href="tel:+15551234567" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        (555) 123-4567
                    </a>
                </div>
            </div>
        </div>

        {{-- Navigation Links --}}
        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Return to Home
            </a>
            
            <div class="flex space-x-6 text-sm text-gray-500">
                <a href="#" class="hover:text-gray-700 transition">Privacy Policy</a>
                <a href="#" class="hover:text-gray-700 transition">Terms of Service</a>
                <a href="#" class="hover:text-gray-700 transition">Cookie Policy</a>
            </div>
        </div>
    </div>
</div>

{{-- Add IDs to headings for TOC navigation --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add IDs to all h2 and h3 elements for anchor linking
        document.querySelectorAll('.prose h2, .prose h3').forEach(function(heading) {
            const id = heading.textContent.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
            heading.id = id;
            heading.classList.add('scroll-mt-20'); // Offset for fixed headers
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(targetId);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        });
    });
</script>
@endpush

<style>
    .prose h2 {
        @apply text-2xl font-bold text-gray-900 mt-8 mb-4;
    }
    .prose h3 {
        @apply text-xl font-semibold text-gray-800 mt-6 mb-3;
    }
    .prose h4 {
        @apply text-lg font-medium text-gray-800 mt-4 mb-2;
    }
    .prose p {
        @apply text-gray-700 leading-relaxed mb-4;
    }
    .prose ul, .prose ol {
        @apply mb-4 pl-6;
    }
    .prose li {
        @apply text-gray-700 mb-2;
    }
    .prose a {
        @apply text-blue-600 hover:text-blue-800 underline;
    }
    .prose blockquote {
        @apply border-l-4 border-gray-300 pl-4 italic text-gray-600 my-6;
    }
    .prose code {
        @apply bg-gray-100 px-2 py-1 rounded text-sm font-mono;
    }
    .prose pre {
        @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto my-6;
    }
    .prose table {
        @apply w-full border-collapse my-6;
    }
    .prose th, .prose td {
        @apply border border-gray-300 px-4 py-2 text-left;
    }
    .prose th {
        @apply bg-gray-50 font-semibold;
    }
</style>
@endsection
