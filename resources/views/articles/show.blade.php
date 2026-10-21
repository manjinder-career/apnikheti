@extends('layouts.app')

@section('title', $article->title . ' — Apni Kheti Apne Beej')

@section('content')

<div class="bg-forest-800 text-white py-12 border-b-4 border-amber-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-2">
        <span class="text-xs bg-amber-500 text-forest-900 font-bold px-3 py-1 rounded-full uppercase">{{ $article->category ? $article->category->name : 'Article' }}</span>
        <h1 class="text-3xl md:text-4xl font-bold font-gurmukhi text-amber-200 mt-2">{{ $article->title }}</h1>
        <p class="text-xs text-emerald-100">Published on {{ $article->created_at->format('M d, Y') }} | By {{ $article->author_name }}</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="bg-white p-8 rounded-2xl border shadow-sm prose prose-stone max-w-none text-xs md:text-sm leading-relaxed text-gray-800 space-y-4">
        {!! nl2br(e($article->content)) !!}
    </div>

    @if($relatedArticles->count() > 0)
        <div class="space-y-4 pt-6 border-t">
            <h3 class="text-lg font-bold font-gurmukhi text-forest-800">ਸੰਬੰਧਿਤ ਲੇਖ (Related Articles)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($relatedArticles as $rel)
                    <div class="bg-stone-50 p-4 rounded-xl border space-y-2">
                        <h4 class="font-bold text-xs text-gray-900 font-gurmukhi">
                            <a href="{{ route('articles.show', $rel->slug) }}" class="hover:text-forest-700">{{ $rel->title }}</a>
                        </h4>
                        <a href="{{ route('articles.show', $rel->slug) }}" class="text-xs font-bold text-emerald-700 hover:underline">Read &rarr;</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection
