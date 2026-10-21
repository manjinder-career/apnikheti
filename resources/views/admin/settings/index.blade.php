@extends('layouts.admin')

@section('title', 'Site Settings')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Site Settings Management</h1>
        <p class="text-xs text-gray-500">Edit founder biography, slogans, verified seed statistics, and contact information without touching source code.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white p-6 rounded-xl border shadow-sm space-y-6">
        @csrf

        <!-- General Slogans -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">1. Initiative Slogans & Branding</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Punjabi Slogan (ਪੰਜਾਬੀ ਸਲੋਗਨ)</label>
                    <input type="text" name="slogan_pa" value="{{ $settings['slogan_pa'] ?? 'ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ' }}" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">English Slogan</label>
                    <input type="text" name="slogan_en" value="{{ $settings['slogan_en'] ?? 'Our Farming. Our Seeds. Our Future.' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
            </div>
        </div>

        <!-- Founder Profile (Amrit Singh Chahal) -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">2. Founder Biography (Amrit Singh Chahal)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Founder / Practitioner Name</label>
                    <input type="text" name="founder_name" value="{{ $settings['founder_name'] ?? 'Amrit Singh Chahal' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Title / Role</label>
                    <input type="text" name="founder_title" value="{{ $settings['founder_title'] ?? 'Seed Conservationist & Natural Farming Advocate' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Founder Biography & Background Summary</label>
                <textarea name="founder_bio" rows="4" class="w-full text-xs p-2.5 border rounded-lg">{{ $settings['founder_bio'] ?? '' }}</textarea>
            </div>
        </div>

        <!-- Seed Collection Statistics Counter -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">3. Seed Collection Statistics</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Verified Seed Variety Count (e.g. 500+)</label>
                    <input type="text" name="verified_seed_count" value="{{ $settings['verified_seed_count'] ?? '500+' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Display Verified Statistic on Homepage</label>
                    <select name="display_verified_stat" class="w-full text-xs p-2.5 border rounded-lg">
                        <option value="1" {{ ($settings['display_verified_stat'] ?? '1') == '1' ? 'selected' : '' }}>Yes — Display Stat</option>
                        <option value="0" {{ ($settings['display_verified_stat'] ?? '1') == '0' ? 'selected' : '' }}>No — Hide Stat</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">4. Editable Contact Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'info@apnikheti.org' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+91 98765 43210' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Location Address</label>
                    <input type="text" name="contact_address" value="{{ $settings['contact_address'] ?? 'Punjab, India' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-lg text-xs shadow">
            Save Site Settings &rarr;
        </button>
    </form>
</div>

@endsection
