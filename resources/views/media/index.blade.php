@extends('layouts.app')

@section('title', 'Farming Videos — Apni Kheti Apne Beej')

@section('content')

<div class="bg-gradient-to-br from-sky-100 via-white to-emerald-50 py-14 border-b border-sky-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-800">{{ __('Watch & learn') }}</p>
        <h1 class="mt-3 text-3xl md:text-5xl font-bold text-forest-800">{{ __('Field stories, shared on video.') }}</h1>
        <p class="text-sm md:text-base text-slate-600 mt-3">{{ __('Interviews, workshops, and demonstrations from our YouTube collection.') }}</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mediaItems as $item)
            <article class="bg-white border border-sky-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-lg transition">
                @if($item->youtube_id)
                    <div class="aspect-video bg-slate-900">
                        <iframe class="w-full h-full" src="https://www.youtube-nocookie.com/embed/{{ rawurlencode($item->youtube_id) }}" title="{{ $item->title }}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                @endif

                <div class="p-5">
                    <span class="inline-flex bg-sky-50 text-sky-800 text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">{{ $item->category ?? 'Educational' }}</span>
                    <h2 class="mt-3 font-bold text-lg text-slate-900 leading-snug">{{ $item->title }}</h2>
                    @if($item->description)
                        <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">{{ $item->description }}</p>
                    @endif
                </div>
            </article>
        @empty
            <div class="md:col-span-2 lg:col-span-3 text-center py-16 rounded-3xl border border-dashed border-sky-200 bg-white text-slate-500 text-sm">
                <div class="text-4xl" aria-hidden="true">▶</div>
                <p class="mt-4 font-semibold text-slate-700">{{ __('Videos are coming soon.') }}</p>
                <p class="mt-1">{{ __('Our team will add new field stories and learning resources here.') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $mediaItems->links() }}
    </div>
</div>

@endsection
