@extends('layouts.app')

@section('title', 'Traditional Seed Library — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200">ਰਵਾਇਤੀ ਬੀਜ ਲਾਇਬ੍ਰੇਰੀ — Traditional Seed Library</h1>
        <p class="text-xs md:text-sm text-emerald-100 mt-2">Search, filter, and discover traditional Punjabi and North Indian seed varieties.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Search & Filter Controls -->
    <div class="bg-white p-6 rounded-2xl border shadow-sm mb-8 space-y-4">
        <form action="{{ route('seeds.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- Search Keyword -->
            <div class="md:col-span-4">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Search Seed Name / Keyword</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="e.g. Bansi, ਕਣਕ, Basmati, Millet..." class="w-full text-xs px-3 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
            </div>

            <!-- Category Filter -->
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Crop Category</label>
                <select name="category" class="w-full text-xs px-3 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->seeds_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Season Filter -->
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Growing Season</label>
                <select name="season" class="w-full text-xs px-3 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
                    <option value="">All Seasons</option>
                    <option value="Rabi" {{ request('season') == 'Rabi' ? 'selected' : '' }}>Rabi (Winter)</option>
                    <option value="Kharif" {{ request('season') == 'Kharif' ? 'selected' : '' }}>Kharif (Monsoon)</option>
                    <option value="Zaid" {{ request('season') == 'Zaid' ? 'selected' : '' }}>Zaid (Summer)</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-2 flex space-x-2">
                <button type="submit" class="flex-1 bg-forest-700 hover:bg-forest-600 text-white font-bold text-xs py-2.5 rounded-lg shadow transition">
                    Filter
                </button>
                <a href="{{ route('seeds.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-xs px-3 py-2.5 rounded-lg transition flex items-center justify-center">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Seed Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($seeds as $seed)
            <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="p-6 space-y-3">
                    <div class="flex justify-between items-start">
                        <span class="bg-forest-100 text-forest-800 text-xs font-semibold px-2.5 py-1 rounded">
                            {{ $seed->category ? $seed->category->name : 'General' }}
                        </span>
                        @if($seed->is_verified)
                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded border border-amber-300">Verified</span>
                        @endif
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 font-gurmukhi">
                        <a href="{{ route('seeds.show', $seed->slug) }}" class="hover:text-forest-700">{{ $seed->name_pa }}</a>
                    </h2>
                    <h3 class="text-xs font-semibold text-gray-600 italic">
                        {{ $seed->name_en }} {{ $seed->scientific_name ? '('.$seed->scientific_name.')' : '' }}
                    </h3>

                    <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed">
                        {{ $seed->description }}
                    </p>

                    <div class="pt-2 text-xs space-y-1 text-gray-500 border-t">
                        <p><strong>Season:</strong> {{ $seed->growing_season ?? 'N/A' }}</p>
                        <p><strong>Origin:</strong> {{ $seed->region_of_origin ?? 'Punjab' }}</p>
                    </div>
                </div>

                <div class="bg-stone-50 p-4 border-t flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-medium">Contributor: {{ $seed->contributor_name ?? 'Apni Kheti' }}</span>
                    <a href="{{ route('seeds.show', $seed->slug) }}" class="text-xs font-bold text-forest-700 hover:underline">
                        View Profile &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-16 bg-white border rounded-2xl space-y-3">
                <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-base font-bold text-gray-700">No traditional seed records found matching your filters.</p>
                <a href="{{ route('seeds.index') }}" class="inline-block text-xs font-bold text-forest-700 hover:underline">Clear search filters</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-10">
        {{ $seeds->links() }}
    </div>
</div>

@endsection
