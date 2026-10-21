@extends('layouts.app')

@section('title', 'Share Your Farming Experience — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold font-gurmukhi text-amber-200">ਤਜਰਬਾ ਸਾਂਝਾ ਕਰੋ — Share Farming Story</h1>
        <p class="text-xs text-emerald-100 mt-1">Share your traditional seed cultivation experience with the agricultural community.</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white p-8 rounded-2xl border shadow-md space-y-6">
        <form action="{{ route('farmer-stories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Farmer Name *</label>
                    <input type="text" name="farmer_name" required value="{{ old('farmer_name') }}" placeholder="Your Name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Crop Name</label>
                    <input type="text" name="crop_name" value="{{ old('crop_name') }}" placeholder="e.g. Desi Bansi Wheat" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Village</label>
                    <input type="text" name="village" value="{{ old('village') }}" placeholder="Village Name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">District</label>
                    <input type="text" name="district" value="{{ old('district') }}" placeholder="District Name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Farming Practice (e.g. Natural Farming / Jeevamrut / Organic)</label>
                <input type="text" name="farming_practice" value="{{ old('farming_practice') }}" placeholder="Practice Name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Your Story / Observations *</label>
                <textarea name="description" rows="4" required placeholder="Describe your experience with traditional seeds, yield, soil quality..." class="w-full text-xs p-3 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">{{ old('description') }}</textarea>
            </div>

            <div class="bg-stone-50 p-3 rounded-lg text-xs text-gray-600">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="display_consent" value="1" checked class="text-forest-700">
                    <span>I consent to publicly sharing this story on the website after admin approval.</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-forest-700 hover:bg-forest-600 text-white font-bold py-3 rounded-xl shadow transition text-xs">
                Submit Story for Approval &rarr;
            </button>
        </form>
    </div>
</div>

@endsection
