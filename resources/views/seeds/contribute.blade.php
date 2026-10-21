@extends('layouts.app')

@section('title', 'Contribute Seed Information — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200">ਬੀਜ ਦੀ ਜਾਣਕਾਰੀ ਦਿਓ — Contribute Seed Information</h1>
        <p class="text-xs md:text-sm text-emerald-100 mt-2">Help document traditional and heritage seed varieties. Submissions are reviewed by website administrators prior to publication.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white p-8 rounded-2xl border shadow-md space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-xl font-bold font-gurmukhi text-forest-800">ਦੇਸੀ ਬੀਜ ਯੋਗਦਾਨ ਫਾਰਮ (Seed Contribution Form)</h2>
            <p class="text-xs text-gray-500 mt-1">Please provide accurate information about the traditional seed variety known to you or your family.</p>
        </div>

        <form action="{{ route('seeds.contribute.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Contributor Profile -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">1. Contributor Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Your Name *</label>
                        <input type="text" name="contributor_name" required value="{{ old('contributor_name') }}" placeholder="Full Name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number (Optional)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91..." class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Village / Town</label>
                        <input type="text" name="village" value="{{ old('village') }}" placeholder="Village Name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">District & State</label>
                        <input type="text" name="district" value="{{ old('district') }}" placeholder="e.g. Faridkot, Punjab" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                </div>
            </div>

            <!-- Seed Information -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">2. Seed & Crop Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Name in Punjabi (ਬੀਜ ਦਾ ਨਾਮ) *</label>
                        <input type="text" name="seed_name_pa" required value="{{ old('seed_name_pa') }}" placeholder="ਮਿਸਾਲ: ਦੇਸੀ ਬੰਸੀ ਕਣਕ" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600 font-gurmukhi">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Name in English</label>
                        <input type="text" name="seed_name_en" value="{{ old('seed_name_en') }}" placeholder="e.g. Desi Bansi Wheat" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Crop Category</label>
                        <select name="crop_category_name" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name_en }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Growing Season</label>
                        <input type="text" name="growing_season" value="{{ old('growing_season') }}" placeholder="e.g. Rabi / Kharif" class="w-full text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Detailed Description & Special Characteristics *</label>
                    <textarea name="description" rows="3" required placeholder="Describe grain appearance, plant height, taste, disease resistance..." class="w-full text-xs p-3 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Traditional Uses / Culinary Uses</label>
                        <textarea name="traditional_uses" rows="2" placeholder="Rotis, medicinal diets, porridge..." class="w-full text-xs p-3 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">{{ old('traditional_uses') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Preservation Method</label>
                        <textarea name="preservation_method" rows="2" placeholder="Stored with neem leaves, ash, earthen pot..." class="w-full text-xs p-3 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">{{ old('preservation_method') }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Upload Photograph (Optional)</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs p-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <!-- Consent & Privacy -->
            <div class="space-y-3 bg-stone-50 p-4 rounded-xl border text-xs text-gray-700">
                <label class="flex items-start space-x-2">
                    <input type="checkbox" name="display_consent" value="1" checked class="mt-0.5 text-forest-700">
                    <span>I consent to having this traditional seed information published in the public database after review.</span>
                </label>
                <label class="flex items-start space-x-2">
                    <input type="checkbox" name="contact_consent" value="1" checked class="mt-0.5 text-forest-700">
                    <span>I consent to allowing website administrators to contact me privately regarding this seed variety.</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-xl shadow-lg text-sm transition">
                Submit Seed Contribution for Review &rarr;
            </button>
        </form>
    </div>
</div>

@endsection
