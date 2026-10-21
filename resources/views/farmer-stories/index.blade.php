@extends('layouts.app')

@section('title', 'Farmer Stories & Experiences — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200">ਕਿਸਾਨਾਂ ਦੇ ਤਜਰਬੇ — Farmer Stories</h1>
            <p class="text-xs md:text-sm text-emerald-100 mt-1">Authentic stories and crop observations shared by traditional farmers.</p>
        </div>
        <a href="{{ route('farmer-stories.create') }}" class="bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs px-5 py-3 rounded-lg shadow transition">
            + Share Your Farming Experience
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @forelse($stories as $story)
            <div class="bg-white border rounded-2xl p-6 space-y-4 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start border-b pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-forest-800">{{ $story->farmer_name }}</h3>
                        <p class="text-xs text-gray-500">{{ $story->village ? $story->village . ', ' : '' }}{{ $story->district }}</p>
                    </div>
                    @if($story->crop_name)
                        <span class="bg-amber-100 text-amber-900 text-xs font-bold px-2.5 py-1 rounded">
                            {{ $story->crop_name }}
                        </span>
                    @endif
                </div>

                <p class="text-xs text-gray-700 leading-relaxed italic">
                    "{{ $story->description }}"
                </p>

                @if($story->farming_practice)
                    <div class="text-xs bg-stone-50 p-2.5 rounded text-gray-600">
                        <strong>Farming Practice:</strong> {{ $story->farming_practice }}
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-2 text-center py-16 text-gray-500 text-sm">
                No farmer stories published yet.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $stories->links() }}
    </div>
</div>

@endsection
