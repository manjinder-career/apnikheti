@extends('layouts.app')

@section('title', 'Photo Gallery — Apni Kheti Apne Beej')

@section('content')
@php
    $galleryImages = $mediaItems->getCollection()->map(fn ($item) => [
        'src' => str_starts_with($item->url_or_path, 'http') ? $item->url_or_path : asset($item->url_or_path),
        'alt' => $item->title,
        'title' => $item->title,
        'category' => $item->category,
        'description' => $item->description,
    ])->values();
@endphp
<section class="bg-gradient-to-br from-sky-100 via-white to-emerald-50 border-b border-sky-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-800">{{ __('From the field') }}</p>
        <h1 class="mt-3 text-4xl md:text-5xl font-bold tracking-tight text-forest-800">{{ __('A living gallery of our work.') }}</h1>
        <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-600">{{ __('Moments from seed saving, community gatherings, and natural farming—curated by our team.') }}</p>
    </div>
</section>

<section
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
    x-data="{
        images: @js($galleryImages),
        isOpen: false,
        currentIndex: 0,
        opener: null,
        openViewer(index, event) {
            this.currentIndex = index;
            this.opener = event.currentTarget;
            this.isOpen = true;
            document.body.classList.add('overflow-hidden');
            this.$nextTick(() => this.$refs.closeButton.focus());
        },
        closeViewer() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
            this.$nextTick(() => this.opener?.focus());
        },
        showPrevious() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        },
        showNext() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },
        handleKeydown(event) {
            if (!this.isOpen) return;
            if (event.key === 'Escape') this.closeViewer();
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                this.showPrevious();
            }
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                this.showNext();
            }
        }
    }"
    @keydown.window="handleKeydown($event)"
>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mediaItems as $item)
            <figure class="group overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-sm">
                <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                    <button
                        type="button"
                        class="block h-full w-full cursor-zoom-in focus:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-sky-600"
                        data-gallery-image
                        @click="openViewer({{ $loop->index }}, $event)"
                        aria-label="View image: {{ $item->title }}"
                    >
                        <img src="{{ str_starts_with($item->url_or_path, 'http') ? $item->url_or_path : asset($item->url_or_path) }}" alt="{{ $item->title }}" loading="lazy" class="h-full w-full object-contain transition duration-300 group-hover:brightness-95">
                    </button>
                </div>
                <figcaption class="p-5">
                    @if($item->category)
                        <span class="text-[11px] font-bold uppercase tracking-wider text-sky-700">{{ $item->category }}</span>
                    @endif
                    <h2 class="mt-1 text-lg font-bold text-slate-900">{{ $item->title }}</h2>
                    @if($item->description)
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $item->description }}</p>
                    @endif
                </figcaption>
            </figure>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 rounded-3xl border border-dashed border-sky-200 bg-white px-6 py-16 text-center text-slate-500">
                <div class="text-4xl" aria-hidden="true">📷</div>
                <p class="mt-4 font-semibold text-slate-700">The gallery is being prepared.</p>
                <p class="mt-1 text-sm">{{ __('Please check back soon for stories from the field.') }}</p>
            </div>
        @endforelse
    </div>
    <div class="mt-10">{{ $mediaItems->links() }}</div>

    <div
        x-show="isOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/95 p-4 sm:p-8"
        role="dialog"
        aria-modal="true"
        aria-label="Photo viewer"
        @click.self="closeViewer()"
    >
        <button
            x-ref="closeButton"
            type="button"
            @click="closeViewer()"
            class="absolute right-4 top-4 z-10 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
            aria-label="Close image viewer"
        >
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path d="m6 6 12 12M18 6 6 18" stroke-linecap="round" stroke-width="2" />
            </svg>
        </button>

        <button
            type="button"
            @click="showPrevious()"
            class="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:left-6"
            aria-label="Previous image"
        >
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
        </button>

        <div class="flex h-full w-full max-w-6xl flex-col items-center justify-center gap-4">
            <div class="flex min-h-0 w-full flex-1 items-center justify-center">
                <img
                    :src="images[currentIndex]?.src"
                    :alt="images[currentIndex]?.alt"
                    class="max-h-full max-w-full object-contain"
                >
            </div>
            <div class="max-w-3xl text-center text-white">
                <p x-show="images[currentIndex]?.category" x-text="images[currentIndex]?.category" class="text-xs font-bold uppercase tracking-widest text-sky-200"></p>
                <h2 x-text="images[currentIndex]?.title" class="mt-1 text-lg font-semibold"></h2>
                <p x-show="images[currentIndex]?.description" x-text="images[currentIndex]?.description" class="mt-1 text-sm text-white/75"></p>
                <p class="mt-2 text-xs text-white/60"><span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span></p>
            </div>
        </div>

        <button
            type="button"
            @click="showNext()"
            class="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:right-6"
            aria-label="Next image"
        >
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
        </button>
    </div>
</section>
@endsection
