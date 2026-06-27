{{-- 
    Template 3: Dynamic Donation Campaign Page
    High-converting layout with progress bars, Alpine.js donation widget, and donor ticker
--}}
@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white py-16" x-data="{ amount: 50, custom: false, frequency: 'once' }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Campaign Header --}}
        <div class="text-center mb-12">
            <span class="inline-block bg-green-100 text-green-800 px-4 py-1 rounded-full text-sm font-bold uppercase tracking-wide mb-4">
                Active Campaign
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">{{ $post->title }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">{{ $post->excerpt }}</p>
        </div>

        {{-- Progress Bar Section --}}
        <div class="max-w-4xl mx-auto mb-12 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-end mb-3">
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase">Total Raised</p>
                    <p class="text-3xl font-bold text-green-600">$12,450</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 font-medium uppercase">Goal</p>
                    <p class="text-2xl font-bold text-gray-900">$50,000</p>
                </div>
            </div>
            
            {{-- Progress Bar --}}
            <div class="w-full bg-gray-200 rounded-full h-5 overflow-hidden mb-2">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-5 rounded-full transition-all duration-1000 ease-out" 
                     style="width: 25%"></div>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-green-600 font-semibold">25% Funded</span>
                <span class="text-gray-500">37 days remaining</span>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">248</p>
                    <p class="text-xs text-gray-500 uppercase">Donors</p>
                </div>
                <div class="text-center border-l border-gray-200">
                    <p class="text-2xl font-bold text-gray-900">$50</p>
                    <p class="text-xs text-gray-500 uppercase">Avg Gift</p>
                </div>
                <div class="text-center border-l border-gray-200">
                    <p class="text-2xl font-bold text-gray-900">12</p>
                    <p class="text-xs text-gray-500 uppercase">Days Left</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            {{-- Story Content --}}
            <div class="bg-white p-8 rounded-2xl shadow-xl">
                @if($post->featured_image)
                <img src="{{ Storage::url($post->featured_image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full h-64 object-cover rounded-lg mb-6">
                @endif
                
                <div class="prose prose-blue max-w-none">
                    {!! $post->body !!}
                </div>

                {{-- Impact Metrics --}}
                <div class="mt-8 bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Your Impact</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">$25 provides school supplies for one child</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">$50 feeds a family for two weeks</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">$100 provides medical care for a month</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Donation Widget --}}
            <div class="bg-white p-8 rounded-2xl shadow-xl border-t-4 border-blue-600 sticky top-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Make an Impact Today</h3>
                
                {{-- Frequency Toggle --}}
                <div class="flex bg-gray-100 rounded-lg p-1 mb-6">
                    <button @click="frequency = 'once'" 
                            :class="frequency === 'once' ? 'bg-white shadow text-blue-600' : 'text-gray-600'"
                            class="flex-1 py-2 rounded-md text-sm font-semibold transition">
                        One-Time
                    </button>
                    <button @click="frequency = 'monthly'" 
                            :class="frequency === 'monthly' ? 'bg-white shadow text-blue-600' : 'text-gray-600'"
                            class="flex-1 py-2 rounded-md text-sm font-semibold transition">
                        Monthly ♡
                    </button>
                </div>

                {{-- Amount Grid --}}
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <template x-for="val in [25, 50, 100]">
                        <button @click="amount = val; custom = false" 
                                :class="amount === val && !custom ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="py-4 rounded-lg font-bold text-lg transition transform hover:scale-105" 
                                x-text="'$' + val">
                        </button>
                    </template>
                </div>
                
                {{-- Custom Amount --}}
                <div class="relative mb-6">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">$</span>
                    <input type="number" 
                           x-model="amount" 
                           @focus="custom = true"
                           min="1"
                           placeholder="Other amount"
                           class="w-full pl-10 pr-12 py-4 border-2 border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-bold text-xl">
                    <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm font-semibold">USD</span>
                </div>

                {{-- Donate Button --}}
                <button class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-5 rounded-lg shadow-lg transform transition hover:-translate-y-1 text-xl mb-4">
                    Donate $<span x-text="amount"></span> <span x-text="frequency === 'monthly' ? '/ month' : ''"></span>
                </button>
                
                {{-- Trust Badges --}}
                <div class="flex items-center justify-center space-x-4 text-xs text-gray-500 mb-6">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Secure SSL
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Tax Deductible
                    </div>
                </div>

                {{-- Recent Donors Ticker --}}
                <div class="pt-6 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-3 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Recent Supporters
                    </p>
                    <div class="flex -space-x-2 mb-3">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=1" alt="Donor">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=2" alt="Donor">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=3" alt="Donor">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=4" alt="Donor">
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs text-gray-500 font-semibold">+42</div>
                    </div>
                    <p class="text-xs text-gray-500">Join 248 others who have donated</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
