@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen py-12" x-data="{ 
    amount: 50, 
    custom: false, 
    frequency: 'one-time',
    paymentMethod: 'card',
    coverFees: true 
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                Make a Difference Today
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Your generous donation helps us continue our mission to create positive change in the community.
            </p>
        </div>

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Donation Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('public.donation.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl p-8">
                    @csrf
                    
                    @if($campaign)
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Donating to:</strong> {{ $campaign->title }}
                            </p>
                        </div>
                    @endif

                    <!-- Amount Selection -->
                    <div class="mb-8">
                        <label class="block text-lg font-semibold text-gray-900 mb-4">Select Amount</label>
                        
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-4">
                            <template x-for="val in [25, 50, 100, 250, 500, 1000]">
                                <button type="button" 
                                        @click="amount = val; custom = false"
                                        :class="amount === val && !custom ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="py-4 rounded-xl font-bold text-lg transition transform hover:scale-105"
                                        x-text="'$' + val"></button>
                            </template>
                        </div>

                        <div class="relative">
                            <span class="absolute left-4 top-4 text-gray-500 text-xl font-semibold">$</span>
                            <input type="number" 
                                   name="amount"
                                   x-model="amount" 
                                   @focus="custom = true"
                                   min="1" 
                                   max="10000"
                                   step="1"
                                   class="w-full pl-10 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-2xl"
                                   placeholder="Custom amount">
                        </div>
                        @error('amount')
                            <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Frequency Selection -->
                    <div class="mb-8">
                        <label class="block text-lg font-semibold text-gray-900 mb-4">Donation Frequency</label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="frequency" value="one-time" x-model="frequency" class="sr-only" checked>
                                <div :class="frequency === 'one-time' ? 'bg-green-100 border-green-500 text-green-800' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'"
                                     class="border-2 rounded-xl p-4 text-center transition">
                                    <div class="text-2xl mb-1">🎯</div>
                                    <div class="font-semibold">One-Time</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="frequency" value="monthly" x-model="frequency" class="sr-only">
                                <div :class="frequency === 'monthly' ? 'bg-blue-100 border-blue-500 text-blue-800' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'"
                                     class="border-2 rounded-xl p-4 text-center transition">
                                    <div class="text-2xl mb-1">📅</div>
                                    <div class="font-semibold">Monthly</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="frequency" value="yearly" x-model="frequency" class="sr-only">
                                <div :class="frequency === 'yearly' ? 'bg-purple-100 border-purple-500 text-purple-800' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'"
                                     class="border-2 rounded-xl p-4 text-center transition">
                                    <div class="text-2xl mb-1">🎉</div>
                                    <div class="font-semibold">Yearly</div>
                                </div>
                            </label>
                        </div>
                        @error('frequency')
                            <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cover Fees Toggle -->
                    <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="cover_fees" x-model="coverFees" class="sr-only">
                            <div :class="coverFees ? 'bg-green-500' : 'bg-gray-300'"
                                 class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="coverFees ? 'translate-x-6' : 'translate-x-1'"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition"></span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-semibold text-gray-900">Cover processing fees</div>
                                <div class="text-xs text-gray-600">100% of your donation goes to the cause (you pay the ~3% fee)</div>
                            </div>
                        </label>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-8">
                        <label class="block text-lg font-semibold text-gray-900 mb-4">Payment Method</label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="sr-only" checked>
                                <div :class="paymentMethod === 'card' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'"
                                     class="border-2 rounded-xl p-4 text-center transition">
                                    <div class="text-2xl mb-2">💳</div>
                                    <div class="text-sm font-semibold">Credit Card</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="paypal" x-model="paymentMethod" class="sr-only">
                                <div :class="paymentMethod === 'paypal' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'"
                                     class="border-2 rounded-xl p-4 text-center transition">
                                    <div class="text-2xl mb-2">🅿️</div>
                                    <div class="text-sm font-semibold">PayPal</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" class="sr-only">
                                <div :class="paymentMethod === 'bank_transfer' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'"
                                     class="border-2 rounded-xl p-4 text-center transition">
                                    <div class="text-2xl mb-2">🏦</div>
                                    <div class="text-sm font-semibold">Bank Transfer</div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Donor Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="donor_name" value="{{ old('donor_name') }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('donor_name')
                                    <p class="mt-1 text-red-600 text-xs">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="donor_email" value="{{ old('donor_email') }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('donor_email')
                                    <p class="mt-1 text-red-600 text-xs">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone (Optional)</label>
                                <input type="tel" name="donor_phone" value="{{ old('donor_phone') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_anonymous" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Make this donation anonymous</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tribute Option -->
                    <div class="mb-8 p-4 bg-purple-50 border border-purple-200 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Honorary or Memorial Gift</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <select name="tribute_type" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">No tribute</option>
                                <option value="honor">In Honor Of</option>
                                <option value="memory">In Memory Of</option>
                            </select>
                            <input type="text" name="tribute_name" placeholder="Person's name"
                                   class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                        <textarea name="message" rows="3" maxlength="500"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="Share why you're donating..."></textarea>
                        @error('message')
                            <p class="mt-1 text-red-600 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-5 rounded-xl shadow-lg transform transition hover:-translate-y-1 text-xl">
                        Donate $<span x-text="amount"></span> <span x-text="frequency === 'one-time' ? 'Now' : (frequency === 'monthly' ? '/month' : '/year')"></span>
                    </button>

                    <p class="mt-4 text-xs text-center text-gray-500 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        Secure payment powered by Stripe. Your information is encrypted and protected.
                    </p>
                </form>
            </div>

            <!-- Impact Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Impact Cards -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Your Impact</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">🍎</div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">$25 provides meals</p>
                                <p class="text-xs text-gray-500">Feed a family for a week</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">📚</div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">$50 supplies education</p>
                                <p class="text-xs text-gray-500">School materials for 5 children</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl">🏥</div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">$100 supports healthcare</p>
                                <p class="text-xs text-gray-500">Medical supplies for clinic</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Donors -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Supporters</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://i.pravatar.cc/100?img=1" class="w-8 h-8 rounded-full">
                                <span class="ml-2 text-sm text-gray-700">John D.</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">$100</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://i.pravatar.cc/100?img=2" class="w-8 h-8 rounded-full">
                                <span class="ml-2 text-sm text-gray-700">Sarah M.</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">$50/mo</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs">A</div>
                                <span class="ml-2 text-sm text-gray-700">Anonymous</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">$250</span>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-gray-500 text-center">+247 others this month</p>
                </div>

                <!-- Tax Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-blue-600 text-xl">ℹ️</div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-blue-900">Tax Deductible</p>
                            <p class="text-xs text-blue-700 mt-1">Your donation is tax-deductible. We'll send you a receipt via email.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
