@extends('layouts.app')

@section('content')
{{-- 
    Blog Index Page
    Lists all published posts with filtering by category and tag
--}}
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Latest News & Updates</h1>
            <p class="mt-4 text-xl text-gray-600">Stay informed about our organization's activities</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content - Posts Grid -->
            <div class="lg:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($posts as $post)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        @if($post->featured_image)
                        <a href="{{ route('public.post.show', $post->slug) }}">
                            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        </a>
                        @endif
                        
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold uppercase">
                                    {{ $post->category->name ?? 'General' }}
                                </span>
                                <span class="ml-3">{{ $post->published_at->format('M d, Y') }}</span>
                            </div>
                            
                            <h2 class="text-xl font-bold text-gray-900 mb-2">
                                <a href="{{ route('public.post.show', $post->slug) }}" class="hover:text-blue-600">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            <p class="text-gray-600 text-sm mb-4">
                                {{ Str::limit($post->excerpt ?? strip_tags($post->body), 120) }}
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $post->author->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($post->author->name) }}" 
                                         class="h-8 w-8 rounded-full mr-2">
                                    <span class="text-sm text-gray-600">{{ $post->author->name }}</span>
                                </div>
                                <a href="{{ route('public.post.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">No posts found yet.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="space-y-6">
                <!-- Search -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Search</h3>
                    <form action="{{ route('public.posts.index') }}" method="GET" class="flex">
                        <input type="text" name="search" placeholder="Search posts..." 
                               class="flex-1 border border-gray-300 rounded-l-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Categories -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Categories</h3>
                    <ul class="space-y-2">
                        @foreach($categories as $category)
                        <li>
                            <a href="{{ route('public.posts.index', ['category' => $category->id]) }}" 
                               class="flex items-center justify-between text-gray-600 hover:text-blue-600">
                                <span>{{ $category->name }}</span>
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                    {{ $category->posts_count ?? 0 }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Tags -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Popular Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags->take(15) as $tag)
                        <a href="{{ route('public.posts.index', ['tag' => $tag->id]) }}" 
                           class="px-3 py-1 bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 rounded-md text-sm transition">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Newsletter Signup -->
                <div class="bg-blue-600 p-6 rounded-lg shadow-md text-white">
                    <h3 class="text-lg font-bold mb-2">Subscribe to Our Newsletter</h3>
                    <p class="text-blue-100 text-sm mb-4">Get the latest updates delivered to your inbox.</p>
                    <form action="#" method="POST" class="space-y-2">
                        @csrf
                        <input type="email" placeholder="Your email address" 
                               class="w-full px-3 py-2 rounded-md text-gray-900 focus:outline-none">
                        <button type="submit" class="w-full bg-white text-blue-600 font-bold py-2 rounded-md hover:bg-blue-50 transition">
                            Subscribe
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
