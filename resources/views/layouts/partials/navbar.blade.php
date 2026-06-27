{{-- Main Navigation Bar --}}
<nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-bold text-blue-600">{{ config('app.name', 'NonProfit') }}</span>
                </a>
                
                {{-- Desktop Menu --}}
                <div class="hidden md:ml-8 md:flex md:space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Home</a>
                    <a href="{{ route('public.posts.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">News</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">About</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Events</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Donate</a>
                </div>
            </div>
            
            {{-- Right Side Actions --}}
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">Join Us</a>
                @endauth
            </div>
            
            {{-- Mobile menu button --}}
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" class="md:hidden bg-white border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Home</a>
            <a href="{{ route('public.posts.index') }}" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">News</a>
            <a href="#" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">About</a>
            <a href="#" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Events</a>
            <a href="#" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Donate</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Login</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium text-blue-600 hover:text-blue-700 hover:bg-gray-50 rounded-md">Join Us</a>
            @endauth
        </div>
    </div>
</nav>
