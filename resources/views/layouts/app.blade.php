<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- Dynamic SEO Meta Tags --}}
    {!! SEO::generate() !!}
    
    {{-- CSRF Token for forms --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Alpine.js for interactivity --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- TinyMCE Rich Text Editor (for admin only, but included here for consistency) --}}
    <script src="https://cdn.tiny.mce.com/1/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    {{-- Navigation --}}
    @include('layouts.partials.navbar')
    
    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('layouts.partials.footer')
    
    @stack('scripts')
</body>
</html>
