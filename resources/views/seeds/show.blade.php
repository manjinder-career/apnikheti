@extends('layouts.app')

@section('title', $seed->name_en . ' (' . $seed->name_pa . ') — Traditional Seed Profile')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-2 mb-2 text-xs text-emerald-200">
            <a href="{{ route('seeds.index') }}" class="hover:underline">Seed Library</a>
            <span>&rsaquo;</span>
            <span>{{ $seed->category ? $seed->category->name : 'Seeds' }}</span>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-5xl font-bold font-gurmukhi text-amber-200">{{ $seed->name_pa }}</h1>
                <h2 class="text-lg md:text-xl font-semibold text-white mt-1">{{ $seed->name_en }} {{ $seed->scientific_name ? '('.$seed->scientific_name.')' : '' }}</h2>
            </div>
            @if($seed->is_verified)
                <span class="inline-flex items-center space-x-1 bg-amber-500 text-forest-900 text-xs font-bold px-3 py-1.5 rounded-full shadow self-start md:self-auto">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Verified Traditional Variety</span>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <!-- Main Details -->
        <div class="md:col-span-8 space-y-8">
            <!-- Descriptions -->
            <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-forest-800 font-gurmukhi border-b pb-2">ਵਿਸਤਾਰਿਤ ਜਾਣਕਾਰੀ (Description)</h3>
                <div class="prose prose-stone text-xs md:text-sm leading-relaxed space-y-3 text-gray-700">
                    @if($seed->description_pa)
                        <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                            <h4 class="font-bold text-forest-800 font-gurmukhi mb-1">ਪੰਜਾਬੀ ਵਿੱਚ:</h4>
                            <p class="font-gurmukhi text-gray-800">{{ $seed->description_pa }}</p>
                        </div>
                    @endif
                    @if($seed->description_en)
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">English Summary:</h4>
                            <p>{{ $seed->description_en }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Agronomic & Cultivation Characteristics Table -->
            <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-forest-800 font-gurmukhi border-b pb-2">ਖੇਤੀਬਾੜੀ ਵਿਸ਼ੇਸ਼ਤਾਵਾਂ (Agronomic Details)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-stone-50 rounded-lg">
                        <span class="block font-bold text-gray-500 uppercase">Region of Origin</span>
                        <span class="font-semibold text-gray-800">{{ $seed->region_of_origin ?? 'Punjab / North India' }}</span>
                    </div>
                    <div class="p-3 bg-stone-50 rounded-lg">
                        <span class="block font-bold text-gray-500 uppercase">Growing Season</span>
                        <span class="font-semibold text-gray-800">{{ $seed->growing_season ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 bg-stone-50 rounded-lg">
                        <span class="block font-bold text-gray-500 uppercase">Sowing Period</span>
                        <span class="font-semibold text-gray-800">{{ $seed->sowing_period ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 bg-stone-50 rounded-lg">
                        <span class="block font-bold text-gray-500 uppercase">Crop Duration</span>
                        <span class="font-semibold text-gray-800">{{ $seed->crop_duration ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 bg-stone-50 rounded-lg">
                        <span class="block font-bold text-gray-500 uppercase">Soil Preferences</span>
                        <span class="font-semibold text-gray-800">{{ $seed->soil_preferences ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 bg-stone-50 rounded-lg">
                        <span class="block font-bold text-gray-500 uppercase">Water Requirements</span>
                        <span class="font-semibold text-gray-800">{{ $seed->water_requirements ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Uses & Farmer Knowledge -->
            <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-forest-800 font-gurmukhi border-b pb-2">ਵਰਤੋਂ ਅਤੇ ਬੀਜ ਸੰਭਾਲ (Uses & Seed Saving)</h3>
                <div class="space-y-4 text-xs text-gray-700">
                    @if($seed->traditional_uses)
                        <div>
                            <h4 class="font-bold text-forest-700 mb-1">Traditional Uses:</h4>
                            <p class="bg-stone-50 p-3 rounded">{{ $seed->traditional_uses }}</p>
                        </div>
                    @endif
                    @if($seed->culinary_uses)
                        <div>
                            <h4 class="font-bold text-forest-700 mb-1">Culinary & Health Uses:</h4>
                            <p class="bg-stone-50 p-3 rounded">{{ $seed->culinary_uses }}</p>
                        </div>
                    @endif
                    @if($seed->farmer_observations)
                        <div>
                            <h4 class="font-bold text-forest-700 mb-1">Farmer Observations:</h4>
                            <p class="bg-amber-50 p-3 rounded border border-amber-200 text-amber-900">{{ $seed->farmer_observations }}</p>
                        </div>
                    @endif
                    @if($seed->preservation_methods)
                        <div>
                            <h4 class="font-bold text-forest-700 mb-1">Seed Preservation Methods:</h4>
                            <p class="bg-emerald-50 p-3 rounded border border-emerald-200 text-emerald-900">{{ $seed->preservation_methods }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info Box -->
        <div class="md:col-span-4 space-y-6">
            <div class="bg-stone-50 border p-6 rounded-2xl space-y-4 shadow-sm">
                <h4 class="font-bold text-sm text-forest-800 uppercase tracking-wider border-b pb-2">Record Metadata</h4>
                <div class="text-xs space-y-2 text-gray-600">
                    <p><strong>Local Names:</strong> {{ $seed->local_names ?? 'N/A' }}</p>
                    <p><strong>Contributed By:</strong> {{ $seed->contributor_name ?? 'Apni Kheti Initiative' }}</p>
                    <p><strong>Category:</strong> {{ $seed->category ? $seed->category->name : 'General' }}</p>
                    <p><strong>Added On:</strong> {{ $seed->created_at->format('M d, Y') }}</p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('seeds.contribute') }}" class="block text-center bg-forest-700 hover:bg-forest-600 text-white text-xs font-bold py-2.5 rounded-lg shadow transition">
                        Contribute Similar Seed
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
