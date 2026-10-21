@extends('layouts.app')

@section('title', 'Apni Kheti Apne Beej — ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ')

@section('content')

<!-- Hero Section -->
<div class="relative overflow-hidden border-b border-sky-100 bg-gradient-to-br from-sky-100 via-white to-emerald-50 py-16 md:py-24">
    <div class="pointer-events-none absolute -right-24 -top-36 h-96 w-96 rounded-full bg-sky-200/50 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-emerald-100/70 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 items-center">
            <div class="md:col-span-7 space-y-6">
                <div class="inline-flex items-center space-x-2 bg-white/80 text-sky-900 px-4 py-2 rounded-full text-xs font-bold tracking-wide border border-sky-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ __('Traditional Seed Preservation Initiative') }}</span>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold font-gurmukhi text-forest-800 leading-tight">
                    ਆਪਣੀ ਖੇਤੀ, ਆਪਣੇ ਬੀਜ
                </h1>
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">
                    {{ __('Our Farming. Our Seeds. Our Future.') }}
                </h2>
                <p class="text-base md:text-lg text-slate-600 max-w-2xl leading-relaxed">
                    {{ __('Preserving traditional seeds, protecting agricultural biodiversity, and sharing knowledge for a healthier and more sustainable future.') }}
                </p>

                <div class="pt-2 flex flex-wrap gap-4">
                    <a href="{{ route('shop.index') }}" class="bg-sky-700 hover:bg-sky-800 text-white font-bold px-6 py-3.5 rounded-xl shadow-lg shadow-sky-900/10 text-sm transition transform hover:-translate-y-0.5">
                        {{ __('Seeds & Kits') }} &rarr;
                    </a>
                    <a href="{{ route('seeds.index') }}" class="bg-white hover:bg-sky-50 text-forest-800 border border-sky-200 font-semibold px-6 py-3.5 rounded-xl shadow-sm text-sm transition">
                        {{ __('Explore Seeds') }}
                    </a>
                </div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 pt-2 text-sm font-semibold text-sky-800">
                    <a href="{{ route('gallery.index') }}" class="hover:text-sky-600">{{ __('Photo Gallery') }} →</a>
                    <a href="{{ route('videos.index') }}" class="hover:text-sky-600">{{ __('YouTube Videos') }} →</a>
                </div>
            </div>

            <div class="md:col-span-5">
                <div class="relative rounded-[2rem] border border-white bg-white/80 p-6 md:p-8 shadow-xl shadow-sky-900/10 backdrop-blur">
                    <div class="flex items-center justify-between border-b border-sky-100 pb-4">
                        <span class="text-xs uppercase font-bold text-sky-800 tracking-wider">Rooted in the land</span>
                        @if($displayVerifiedStat)
                            <span class="bg-emerald-50 text-emerald-800 text-xs font-bold px-3 py-1.5 rounded-full">{{ $verifiedSeedStat }} records</span>
                        @endif
                    </div>
                    <div class="py-7 text-center">
                        <div class="mx-auto flex h-32 w-32 items-center justify-center rounded-full bg-gradient-to-br from-sky-100 to-emerald-100 text-7xl shadow-inner" aria-hidden="true">🌱</div>
                        <h3 class="mt-5 text-2xl font-bold text-forest-800 font-gurmukhi">ਬੀਜਾਂ ਤੋਂ ਭਵਿੱਖ ਤੱਕ</h3>
                        <p class="mt-2 text-sm text-slate-500">Keeping heritage seeds and farmer knowledge alive.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-sky-50 p-4">
                            <div class="text-2xl" aria-hidden="true">🌾</div>
                            <div>
                                <h4 class="mt-2 font-bold text-sm text-slate-800 font-gurmukhi">ਦੇਸੀ ਬੰਸੀ ਕਣਕ</h4>
                                <p class="text-xs text-slate-500">Traditional wheat</p>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-emerald-50 p-4">
                            <div class="text-2xl" aria-hidden="true">🌿</div>
                            <div>
                                <h4 class="mt-2 font-bold text-sm text-slate-800 font-gurmukhi">ਰਵਾਇਤੀ ਬੀਜ</h4>
                                <p class="text-xs text-slate-500">Saved with care</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="relative z-10 -mt-1 bg-white py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('shop.index') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-100 bg-sky-50/70 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-2xl shadow-sm" aria-hidden="true">🛍️</span>
            <span><strong class="block text-sm text-forest-800">Seeds & seed kits</strong><span class="text-xs text-slate-500">Browse what's available</span></span>
        </a>
        <a href="{{ route('gallery.index') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-100 bg-sky-50/70 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-2xl shadow-sm" aria-hidden="true">📷</span>
            <span><strong class="block text-sm text-forest-800">Photo gallery</strong><span class="text-xs text-slate-500">See life in the fields</span></span>
        </a>
        <a href="{{ route('videos.index') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-100 bg-sky-50/70 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-2xl shadow-sm" aria-hidden="true">▶️</span>
            <span><strong class="block text-sm text-forest-800">Videos & learning</strong><span class="text-xs text-slate-500">Watch our YouTube stories</span></span>
        </a>
    </div>
</section>

<!-- Section 1 & 2: Initiative Introduction & Stats -->
<section class="py-16 bg-cream/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-forest-600">Initiative Vision</span>
                <h2 class="text-3xl font-bold font-gurmukhi text-forest-800">
                    ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ — ਪਰੰਪਰਾਗਤ ਬੀਜਾਂ ਦੀ ਸੰਭਾਲ
                </h2>
                <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                    Associated with <strong>{{ $founderName }}</strong>, the initiative "Apni Kheti Apne Beej" is dedicated to the preservation, documentation, and cultivation of traditional seeds. Over decades of intensive agriculture, indigenous seeds have been replaced by hybrid varieties requiring chemical dependency. Our initiative works directly with farmers to restore traditional seed varieties and chemical-free sustainable farming practices across Punjab.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-3">
                        <div class="w-8 h-8 rounded bg-forest-100 text-forest-700 flex items-center justify-center font-bold text-lg">✓</div>
                        <div>
                            <h4 class="font-bold text-xs uppercase text-forest-700">Seed Sovereignty</h4>
                            <p class="text-xs text-gray-600">Empowering farmers to select, save, and exchange their own seeds.</p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-3">
                        <div class="w-8 h-8 rounded bg-forest-100 text-forest-700 flex items-center justify-center font-bold text-lg">✓</div>
                        <div>
                            <h4 class="font-bold text-xs uppercase text-forest-700">Soil Restoration</h4>
                            <p class="text-xs text-gray-600">Promoting natural composting, jeevamrut, and bio-diverse cropping.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Box -->
            <div class="lg:col-span-5">
                <div class="bg-gradient-to-br from-forest-700 to-forest-800 text-white p-8 rounded-2xl shadow-xl space-y-6">
                    <h3 class="text-xl font-bold font-gurmukhi text-amber-300">ਬੀਜ ਸੰਭਾਲ ਦੇ ਅੰਕੜੇ</h3>
                    @if($displayVerifiedStat)
                        <div class="border-b border-emerald-600 pb-4">
                            <span class="block text-5xl font-black text-amber-400">{{ $verifiedSeedStat }}</span>
                            <span class="text-sm font-medium text-emerald-100">Documented & Verified Seed Varieties</span>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-forest-900/50 p-3 rounded-lg border border-emerald-600">
                            <span class="block text-2xl font-bold text-white">{{ $categories->count() }}</span>
                            <span class="text-xs text-emerald-200">Crop Categories</span>
                        </div>
                        <div class="bg-forest-900/50 p-3 rounded-lg border border-emerald-600">
                            <span class="block text-2xl font-bold text-white">100%</span>
                            <span class="text-xs text-emerald-200">Chemical Free Focus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Featured Traditional Seed Varieties -->
<section class="py-16 bg-white border-t border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Heritage Database</span>
                <h2 class="text-3xl font-bold text-forest-800 font-gurmukhi">ਪਰੰਪਰਾਗਤ ਬੀਜਾਂ ਦੀ ਲਾਇਬ੍ਰੇਰੀ</h2>
                <p class="text-sm text-gray-600 mt-1">Explore verified traditional seed varieties preserved by local farmers.</p>
            </div>
            <a href="{{ route('seeds.index') }}" class="mt-4 md:mt-0 text-xs font-bold text-forest-700 hover:text-forest-600 bg-forest-50 px-4 py-2 rounded-lg border border-forest-200">
                View All Varieties &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($featuredSeeds as $seed)
                <div class="bg-stone-50 border border-stone-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="bg-forest-100 text-forest-800 text-xs font-semibold px-2.5 py-1 rounded">
                                {{ $seed->category ? $seed->category->name : 'General' }}
                            </span>
                            @if($seed->is_verified)
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded border border-amber-300">Verified</span>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 font-gurmukhi">
                            {{ $seed->name_pa }}
                        </h3>
                        <h4 class="text-sm font-semibold text-gray-600 italic">
                            {{ $seed->name_en }}
                        </h4>

                        <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed">
                            {{ $seed->description }}
                        </p>

                        <div class="pt-2 text-xs space-y-1 text-gray-500 border-t">
                            <p><strong>Growing Season:</strong> {{ $seed->growing_season ?? 'N/A' }}</p>
                            <p><strong>Region:</strong> {{ $seed->region_of_origin ?? 'Punjab' }}</p>
                        </div>
                    </div>

                    <div class="bg-stone-100 p-4 border-t flex justify-between items-center">
                        <span class="text-xs text-gray-500 font-medium">Contributed by: {{ $seed->contributor_name ?? 'Apni Kheti' }}</span>
                        <a href="{{ route('seeds.show', $seed->slug) }}" class="text-xs font-bold text-forest-700 hover:underline">
                            Details &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8 text-gray-500 text-sm">
                    No featured seeds published yet.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Section 4 & 5: Natural & Organic Farming Highlights -->
<section class="py-16 bg-forest-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12 space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-forest-600">Knowledge Center</span>
            <h2 class="text-3xl font-bold text-forest-800 font-gurmukhi">ਕੁਦਰਤੀ ਖੇਤੀ ਅਤੇ ਜੈਵਿਕ ਤਕਨੀਕਾਂ</h2>
            <p class="text-sm text-gray-600">Sustainable agricultural practices for soil enrichment, pest management, and water conservation.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border shadow-sm space-y-3">
                <div class="w-10 h-10 rounded bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">1</div>
                <h3 class="font-bold text-base text-forest-800 font-gurmukhi">ਜੀਵਾਮ੍ਰਿਤ ਅਤੇ ਮਿੱਟੀ ਦੀ ਸਿਹਤ</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Preparation of cow-dung based microbial formulations to regenerate beneficial soil microbes and earthworm populations.</p>
                <a href="{{ route('organic-farming') }}" class="inline-block text-xs font-bold text-emerald-700 hover:underline">Read Guide &rarr;</a>
            </div>

            <div class="bg-white p-6 rounded-xl border shadow-sm space-y-3">
                <div class="w-10 h-10 rounded bg-amber-100 text-amber-700 flex items-center justify-center font-bold">2</div>
                <h3 class="font-bold text-base text-forest-800 font-gurmukhi">ਰਵਾਇਤੀ ਬੀਜਾਂ ਦੀ ਚੋਣ</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Field-level seed selection from healthy standing crop patches and sun-drying techniques for long-term storage.</p>
                <a href="{{ route('organic-farming') }}" class="inline-block text-xs font-bold text-emerald-700 hover:underline">Read Guide &rarr;</a>
            </div>

            <div class="bg-white p-6 rounded-xl border shadow-sm space-y-3">
                <div class="w-10 h-10 rounded bg-earth-100 text-earth-700 flex items-center justify-center font-bold">3</div>
                <h3 class="font-bold text-base text-forest-800 font-gurmukhi">ਕੁਦਰਤੀ ਕੀਟ ਪ੍ਰਬੰਧਨ</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Botanical sprays using neem leaves, sour buttermilk, and local herbs for chemical-free insect management.</p>
                <a href="{{ route('organic-farming') }}" class="inline-block text-xs font-bold text-emerald-700 hover:underline">Read Guide &rarr;</a>
            </div>
        </div>
    </div>
</section>

<!-- Section 6: Farmer Stories Preview -->
<section class="py-16 bg-white border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Farmer Experiences</span>
                <h2 class="text-3xl font-bold text-forest-800 font-gurmukhi">ਕਿਸਾਨਾਂ ਦੇ ਤਜਰਬੇ ਅਤੇ ਕਹਾਣੀਆਂ</h2>
                <p class="text-sm text-gray-600 mt-1">Real observations and experiences shared by traditional farmers.</p>
            </div>
            <a href="{{ route('farmer-stories.index') }}" class="mt-4 md:mt-0 text-xs font-bold text-forest-700 hover:text-forest-600 bg-forest-50 px-4 py-2 rounded-lg border border-forest-200">
                Read All Stories &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($farmerStories as $story)
                <div class="bg-stone-50 border p-6 rounded-2xl space-y-3">
                    <div class="flex justify-between items-center text-xs text-gray-500 border-b pb-2">
                        <span class="font-bold text-forest-800">{{ $story->farmer_name }}</span>
                        <span>{{ $story->village }}, {{ $story->district }}</span>
                    </div>
                    <span class="inline-block text-xs font-semibold bg-amber-100 text-amber-800 px-2 py-0.5 rounded">Crop: {{ $story->crop_name ?? 'Desi Seed' }}</span>
                    <p class="text-xs text-gray-700 italic leading-relaxed">
                        "{{ $story->description }}"
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Community Contribution Callout Banner -->
<section class="py-12 bg-forest-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <h2 class="text-2xl md:text-3xl font-bold font-gurmukhi text-amber-300">ਕੀ ਤੁਹਾਡੇ ਕੋਲ ਪਰੰਪਰਾਗਤ ਬੀਜਾਂ ਦੀ ਜਾਣਕਾਰੀ ਹੈ?</h2>
        <p class="text-sm text-emerald-100 max-w-2xl mx-auto">
            Help build the digital repository of traditional Punjabi seeds. Submit your traditional seed information for review by our administrative team.
        </p>
        <div class="pt-2">
            <a href="{{ route('seeds.contribute') }}" class="inline-block bg-amber-600 hover:bg-amber-500 text-white font-bold px-8 py-3 rounded-lg shadow transition">
                Submit Seed Information Now
            </a>
        </div>
    </div>
</section>

@endsection
