{{-- 
    Template 4: Event & Volunteer Hub Page
    Beautiful layout with event date badges, venue map placeholders, and volunteer registration CTA
--}}
@extends('layouts.app')

@section('content')
<div class="bg-gray-900 text-white min-h-screen">
    {{-- Hero Section with Date Badge --}}
    <div class="relative bg-gray-800 overflow-hidden">
        <div class="absolute inset-0">
            @if($post->featured_image)
            <img src="{{ Storage::url($post->featured_image) }}" 
                 alt="{{ $post->title }}" 
                 class="w-full h-full object-cover opacity-30">
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900 to-transparent"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 py-24 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center">
            <div class="md:w-2/3">
                <span class="inline-block bg-yellow-400 text-gray-900 px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide mb-4">
                    📅 Upcoming Event
                </span>
                <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
                    {{ $post->title }}
                </h1>
                <p class="text-xl text-gray-300 max-w-2xl mb-8">
                    {{ $post->excerpt }}
                </p>
                
                {{-- Event Details Inline --}}
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center text-gray-300">
                        <svg class="w-6 h-6 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs uppercase text-gray-500">Date</p>
                            <p class="font-semibold">{{ $post->published_at->format('l, F j, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center text-gray-300">
                        <svg class="w-6 h-6 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs uppercase text-gray-500">Time</p>
                            <p class="font-semibold">{{ $post->published_at->format('g:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center text-gray-300">
                        <svg class="w-6 h-6 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs uppercase text-gray-500">Location</p>
                            <p class="font-semibold">TBA</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Floating Date Card --}}
            <div class="mt-12 md:mt-0 md:ml-12 bg-white text-gray-900 p-8 rounded-2xl shadow-2xl transform rotate-2 hover:rotate-0 transition duration-300 text-center">
                <div class="text-sm font-bold text-gray-500 uppercase tracking-wide">{{ $post->published_at->format('l') }}</div>
                <div class="text-7xl font-black text-blue-600 my-4">{{ $post->published_at->day }}</div>
                <div class="text-2xl font-bold text-gray-900">{{ $post->published_at->format('F Y') }}</div>
                <div class="text-lg text-gray-600 mt-2 font-medium">{{ $post->published_at->format('g:i A') }}</div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                        ✓ Open Registration
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Event Details --}}
        <div class="lg:col-span-2">
            <div class="prose prose-invert prose-lg max-w-none">
                <h2 class="text-3xl font-bold text-white mb-6">Event Details</h2>
                {!! $post->body !!}
            </div>
            
            {{-- Schedule Timeline --}}
            <div class="mt-12">
                <h3 class="text-2xl font-bold text-white mb-6">Schedule</h3>
                <div class="space-y-4">
                    <div class="flex items-start bg-gray-800 p-4 rounded-lg">
                        <div class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-center min-w-[80px]">
                            <div class="text-xs">9:00</div>
                            <div class="text-xs">AM</div>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-white">Registration & Check-in</h4>
                            <p class="text-gray-400 text-sm">Pick up your badge and welcome kit</p>
                        </div>
                    </div>
                    <div class="flex items-start bg-gray-800 p-4 rounded-lg">
                        <div class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-center min-w-[80px]">
                            <div class="text-xs">10:00</div>
                            <div class="text-xs">AM</div>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-white">Opening Ceremony</h4>
                            <p class="text-gray-400 text-sm">Welcome address and keynote</p>
                        </div>
                    </div>
                    <div class="flex items-start bg-gray-800 p-4 rounded-lg">
                        <div class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-center min-w-[80px]">
                            <div class="text-xs">12:00</div>
                            <div class="text-xs">PM</div>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-white">Lunch Break</h4>
                            <p class="text-gray-400 text-sm">Networking lunch provided</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Map Placeholder --}}
            <div class="mt-12">
                <h3 class="text-2xl font-bold text-white mb-6">Venue Location</h3>
                <div class="bg-gray-800 h-80 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-600">
                    <div class="text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <p class="text-lg font-medium">Interactive Map Loading...</p>
                        <p class="text-sm">Google Maps integration coming soon</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Registration Form Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white text-gray-900 rounded-2xl shadow-2xl p-8 sticky top-6">
                <h3 class="text-2xl font-bold mb-2">Volunteer Registration</h3>
                <p class="text-gray-600 mb-6 text-sm">Join us as a volunteer for this event!</p>
                
                <form action="#" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3">
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role Interest *</label>
                        <select name="role" id="role" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3">
                            <option value="">Select a role...</option>
                            <option value="general">General Volunteer</option>
                            <option value="registration">Registration Desk</option>
                            <option value="setup">Setup/Breakdown Crew</option>
                            <option value="usher">Event Usher</option>
                            <option value="firstaid">First Aid Support</option>
                            <option value="photography">Photography/Videography</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                        <select name="availability" id="availability" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3">
                            <option value="full">Full Day</option>
                            <option value="morning">Morning Only</option>
                            <option value="afternoon">Afternoon Only</option>
                        </select>
                    </div>
                    
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="terms" 
                               id="terms" 
                               required
                               class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            I agree to the volunteer terms and conditions *
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-lg hover:bg-blue-700 transition transform hover:-translate-y-0.5 shadow-lg">
                        Register Now
                    </button>
                </form>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Spots Available:</span>
                        <span class="font-bold text-green-600">23 / 50</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 54%"></div>
                    </div>
                    <p class="text-xs text-center text-red-500 mt-3 font-semibold">⚡ Limited spots available!</p>
                </div>
                
                {{-- Contact Info --}}
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500 mb-2">Questions? Contact us:</p>
                    <a href="mailto:volunteers@nonprofit.org" class="text-sm text-blue-600 hover:underline font-medium">
                        volunteers@nonprofit.org
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
