@extends('layouts.app')

@section('title', 'Contact Us — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200">ਸੰਪਰਕ ਕਰੋ — Contact Us</h1>
        <p class="text-xs md:text-sm text-emerald-100 mt-1">Get in touch regarding traditional seed exchange, natural farming workshops, or general inquiries.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <!-- Contact Information -->
        <div class="md:col-span-5 bg-forest-700 text-white p-8 rounded-2xl shadow-lg space-y-6">
            <h2 class="text-2xl font-bold font-gurmukhi text-amber-300">ਸੰਪਰਕ ਜਾਣਕਾਰੀ</h2>
            <div class="space-y-4 text-xs md:text-sm">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <div>
                        <span class="block font-bold text-amber-200">Email Address</span>
                        <span>{{ $contactEmail }}</span>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <div>
                        <span class="block font-bold text-amber-200">Phone Contact</span>
                        <span>{{ $contactPhone }}</span>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <div>
                        <span class="block font-bold text-amber-200">Location</span>
                        <span>{{ $contactAddress }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="md:col-span-7 bg-white p-8 rounded-2xl border shadow-sm space-y-4">
            <h2 class="text-xl font-bold text-gray-900 font-gurmukhi border-b pb-2">ਸੰਦੇਸ਼ ਭੇਜੋ (Send Message)</h2>
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Your Name *</label>
                        <input type="text" name="name" required placeholder="Full Name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" placeholder="+91..." class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Subject</label>
                        <input type="text" name="subject" value="{{ request('subject') }}" placeholder="Inquiry Topic" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Message *</label>
                    <textarea name="message" rows="4" required placeholder="Write your message here..." class="w-full text-xs p-3 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600"></textarea>
                </div>

                <div class="text-xs text-gray-600">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="consent" value="1" required class="text-forest-700">
                        <span>I consent to having Apni Kheti Apne Beej store my submission for answering my inquiry.</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-forest-700 hover:bg-forest-600 text-white font-bold py-3 rounded-xl shadow transition text-xs">
                    Send Message &rarr;
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
