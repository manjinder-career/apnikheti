@extends('layouts.app')

@section('title', 'About Initiative — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-12 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-5xl font-bold font-gurmukhi text-amber-200">ਪਹਿਲਕਦਮੀ ਬਾਰੇ — About Initiative</h1>
        <p class="text-sm md:text-base text-emerald-100 mt-2">Preserving Punjab's indigenous seeds, soil health, and traditional agricultural heritage.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    <!-- Initiative Mission & Background -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <div class="md:col-span-7 space-y-4">
            <h2 class="text-2xl font-bold font-gurmukhi text-forest-800">"ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ" ਦਾ ਮਕਸਦ</h2>
            <p class="text-sm text-gray-700 leading-relaxed">
                "Apni Kheti Apne Beej" is a dedicated initiative created to preserve traditional seeds, protect agricultural biodiversity, and promote chemical-free natural farming. Traditional seeds evolved over centuries to thrive in local soils and micro-climates, showing natural resilience to heat stress, pest attacks, and drought.
            </p>
            <p class="text-sm text-gray-700 leading-relaxed">
                Our central mission is to empower farmers to reclaim seed sovereignty — allowing them to select, save, and exchange open-pollinated traditional seeds without continuous dependency on commercial chemical-heavy hybrid seeds.
            </p>

            <div class="border-l-4 border-amber-500 bg-amber-50 p-4 rounded-r text-xs text-amber-900 italic font-medium">
                "Seed sovereignty is the foundation of food security, soil health, and farmer autonomy."
            </div>
        </div>

        <div class="md:col-span-5 bg-stone-50 border p-6 rounded-2xl space-y-4 shadow-sm">
            <h3 class="text-lg font-bold text-forest-800 font-gurmukhi border-b pb-2">About Founder / Practitioner</h3>
            <div>
                <h4 class="font-bold text-base text-gray-900">{{ $founderName }}</h4>
                <p class="text-xs text-emerald-700 font-semibold mb-2">{{ $founderTitle }}</p>
                <p class="text-xs text-gray-600 leading-relaxed">
                    {{ $founderBio }}
                </p>
            </div>
            <div class="pt-2 border-t text-xs text-gray-500 space-y-1">
                <p><strong>Verified Seed Collection:</strong> {{ $verifiedCount }} documented varieties</p>
                <p><strong>Published Seed Records:</strong> {{ $totalSeeds }} active in database</p>
            </div>
        </div>
    </div>

    <!-- Core Pillars -->
    <div class="space-y-6">
        <h2 class="text-2xl font-bold font-gurmukhi text-forest-800 text-center">ਖੇਤੀਬਾੜੀ ਵਿਰਾਸਤ ਦੇ ਮੁੱਖ ਥੰਮ੍ਹ (Key Pillars)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border shadow-sm space-y-2">
                <h3 class="font-bold text-base text-forest-700 font-gurmukhi">੧. ਦੇਸੀ ਬੀਜ ਸੰਭਾਲ (Seed Conservation)</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Collecting, documenting, and multiplying traditional landrace seeds of wheat, basmati rice, millets, pulses, oilseeds, and vegetables.</p>
            </div>
            <div class="bg-white p-6 rounded-xl border shadow-sm space-y-2">
                <h3 class="font-bold text-base text-forest-700 font-gurmukhi">੨. ਕੁਦਰਤੀ ਖੇਤੀ ਗਿਆਨ (Natural Farming)</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Disseminating practical techniques for soil organic matter enrichment, jeevamrut preparation, vermicomposting, and bio-pest management.</p>
            </div>
            <div class="bg-white p-6 rounded-xl border shadow-sm space-y-2">
                <h3 class="font-bold text-base text-forest-700 font-gurmukhi">੩. ਕਿਸਾਨ-ਤੋਂ-ਕਿਸਾਨ ਗਿਆਨ ਸਾਂਝ (Knowledge Sharing)</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Creating peer-to-peer learning networks where experienced traditional farmers share field observations and seed preservation methods.</p>
            </div>
        </div>
    </div>

    <!-- Collaborating Organizations -->
    @if($organizations->count() > 0)
        <div class="space-y-6 pt-6 border-t">
            <h2 class="text-2xl font-bold font-gurmukhi text-forest-800">ਸਹਿਯੋਗੀ ਸੰਸਥਾਵਾਂ (Collaborating Organizations)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($organizations as $org)
                    <div class="bg-stone-50 border p-5 rounded-xl space-y-2">
                        <h3 class="font-bold text-base text-forest-800">{{ $org->name }}</h3>
                        <p class="text-xs text-amber-800 font-semibold bg-amber-100 inline-block px-2 py-0.5 rounded">{{ $org->collaboration_type ?? 'Collaborator' }}</p>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $org->description }}</p>
                        @if($org->website_url)
                            <a href="{{ $org->website_url }}" target="_blank" class="inline-block text-xs font-bold text-emerald-700 hover:underline">Visit Website &rarr;</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection
