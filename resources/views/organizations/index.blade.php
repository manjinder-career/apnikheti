@extends('layouts.app')

@section('title', 'Collaborating Organizations — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200">ਸਹਿਯੋਗੀ ਸੰਸਥਾਵਾਂ — Organizations</h1>
        <p class="text-xs md:text-sm text-emerald-100 mt-1">Verified partner organizations and collaborative initiatives in seed conservation.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @forelse($organizations as $org)
            <div class="bg-white border rounded-2xl p-6 space-y-3 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <h3 class="text-xl font-bold text-forest-800">{{ $org->name }}</h3>
                    @if($org->collaboration_type)
                        <span class="bg-amber-100 text-amber-900 text-xs font-semibold px-2.5 py-0.5 rounded">
                            {{ $org->collaboration_type }}
                        </span>
                    @endif
                </div>

                <p class="text-xs text-gray-700 leading-relaxed">
                    {{ $org->description }}
                </p>

                @if($org->projects_summary)
                    <div class="bg-stone-50 p-3 rounded-lg text-xs text-gray-600">
                        <strong>Joint Projects:</strong> {{ $org->projects_summary }}
                    </div>
                @endif

                @if($org->website_url)
                    <div class="pt-2 border-t">
                        <a href="{{ $org->website_url }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:underline">
                            Visit Organization Website &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-2 text-center py-12 text-gray-500 text-sm">
                No organization profiles published yet.
            </div>
        @endforelse
    </div>
</div>

@endsection
