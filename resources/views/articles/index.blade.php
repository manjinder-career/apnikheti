@extends('layouts.app')

@section('title', 'Articles & Educational Resources — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-10 border-b-4 border-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200">ਲੇਖ ਅਤੇ ਮਾਰਗਦਰਸ਼ਨ — Articles & Resources</h1>
        <p class="text-xs md:text-sm text-emerald-100 mt-1">Educational guides on traditional seeds, natural farming, and sustainable agriculture.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-xl border shadow-sm mb-8">
        <form action="{{ route('articles.index') }}" method="GET" class="flex gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search articles..." class="flex-1 text-xs px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
            <button type="submit" class="bg-forest-700 hover:bg-forest-600 text-white font-bold text-xs px-5 py-2 rounded-lg shadow transition">Search</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($articles as $article)
            <div class="bg-white border rounded-2xl p-6 space-y-3 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="space-y-3">
                    <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded">
                        {{ $article->category ? $article->category->name : 'General' }}
                    </span>

                    <h2 class="text-lg font-bold text-gray-900 font-gurmukhi">
                        <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-forest-700">{{ $article->title }}</a>
                    </h2>

                    <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed">
                        {{ $article->excerpt }}
                    </p>
                </div>

                <div class="pt-4 border-t flex justify-between items-center text-xs text-gray-500">
                    <span>By {{ $article->author_name }}</span>
                    <a href="{{ route('articles.show', $article->slug) }}" class="font-bold text-forest-700 hover:underline">Read &rarr;</a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-gray-500 text-sm">
                No articles published yet.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $articles->links() }}
    </div>
</div>

@endsection
