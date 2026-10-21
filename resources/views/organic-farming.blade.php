@extends('layouts.app')

@section('title', 'Organic & Natural Farming — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200">ਕੁਦਰਤੀ ਖੇਤੀ ਅਤੇ ਜੈਵਿਕ ਤਕਨੀਕਾਂ</h1>
        <p class="text-xs md:text-sm text-emerald-100 mt-2">Knowledge center for soil organic matter enrichment, composting, and chemical-free agriculture.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    <!-- Featured Topics Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-2">
            <h3 class="font-bold text-base text-forest-800 font-gurmukhi">ਮਿੱਟੀ ਦੀ ਸਿਹਤ (Soil Health)</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Increasing soil organic carbon through farmyard manure, green manure crops (dhaincha/sunhemp), and earthworm activation.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-2">
            <h3 class="font-bold text-base text-forest-800 font-gurmukhi">ਜੀਵਾਮ੍ਰਿਤ ਅਤੇ ਘਨਜੀਵਾਮ੍ਰਿਤ</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Fermenting cow dung, cow urine, pulse flour, and jaggery to multiply beneficial soil bacteria for nutrient availability.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-2">
            <h3 class="font-bold text-base text-forest-800 font-gurmukhi">ਕੁਦਰਤੀ ਕੀਟ ਪ੍ਰਬੰਧਨ (Pest Control)</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Preparing neem-astra, brahmastra, and sour curd sprays to control sucking pests and leaf eaters without synthetic toxins.</p>
        </div>
    </div>

    <!-- Guides List -->
    <div class="space-y-6">
        <h2 class="text-2xl font-bold font-gurmukhi text-forest-800 border-b pb-2">ਕੁਦਰਤੀ ਖੇਤੀ ਲੇਖ ਅਤੇ ਮਾਰਗਦਰਸ਼ਨ (Articles & Guides)</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($articles as $article)
                <div class="bg-white border rounded-2xl p-6 space-y-3 shadow-sm hover:shadow-md transition">
                    <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded">
                        {{ $article->category ? $article->category->name : 'Natural Farming' }}
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 font-gurmukhi">
                        <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-forest-700">{{ $article->title }}</a>
                    </h3>
                    <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed">
                        {{ $article->excerpt }}
                    </p>
                    <div class="pt-2 flex justify-between items-center text-xs text-gray-500 border-t">
                        <span>By {{ $article->author_name }}</span>
                        <a href="{{ route('articles.show', $article->slug) }}" class="font-bold text-forest-700 hover:underline">Read Full Article &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-12 text-gray-500 text-sm">
                    No natural farming guides published yet.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    </div>
</div>

@endsection
