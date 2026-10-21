@extends('layouts.app')

@section('title', $product->name . ' — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <div class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:underline">Home</a>
        <span>&rsaquo;</span>
        <a href="{{ route('shop.index') }}" class="hover:underline">Shop</a>
        @if($product->category)
            <span>&rsaquo;</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:underline">{{ $product->category->name }}</a>
        @endif
        <span>&rsaquo;</span>
        <span class="font-bold text-slate-700 truncate">{{ $product->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 bg-white p-6 md:p-10 rounded-3xl border border-sky-100 shadow-sm">
        <!-- Product Image Showcase -->
        <div class="space-y-4">
            <div class="aspect-[4/3] rounded-3xl overflow-hidden bg-gradient-to-br from-sky-100 to-emerald-50 border border-sky-100 flex items-center justify-center">
                @if($product->photoUrl())
                    <img src="{{ $product->photoUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-8xl">{{ $product->product_type === 'kit' ? '🧺' : '🌱' }}</span>
                @endif
            </div>

            <!-- Guarantee badges -->
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-sky-50/60 p-3 rounded-2xl border border-sky-100">
                    <span class="text-xl block mb-1">🌾</span>
                    <span class="text-[11px] font-bold text-slate-800 block">100% Desi Seed</span>
                    <span class="text-[10px] text-slate-500">Non-hybrid heirloom</span>
                </div>
                <div class="bg-emerald-50/60 p-3 rounded-2xl border border-emerald-100">
                    <span class="text-xl block mb-1">📦</span>
                    <span class="text-[11px] font-bold text-slate-800 block">Safe Transit</span>
                    <span class="text-[10px] text-slate-500">Moisture-proof packs</span>
                </div>
                <div class="bg-amber-50/60 p-3 rounded-2xl border border-amber-100">
                    <span class="text-xl block mb-1">🚚</span>
                    <span class="text-[11px] font-bold text-slate-800 block">Free Shipping</span>
                    <span class="text-[10px] text-slate-500">On orders ₹500+</span>
                </div>
            </div>
        </div>

        <!-- Product Purchase Information -->
        <div class="flex flex-col justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-bold text-sky-800 uppercase tracking-wider">
                        {{ $product->product_type === 'kit' ? 'Seed Starter Kit' : 'Traditional Seed' }}
                    </span>
                    @if($product->category)
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                            {{ $product->category->name }}
                        </span>
                    @endif
                    @if($product->sku)
                        <span class="text-xs text-slate-400 font-mono ml-auto">SKU: {{ $product->sku }}</span>
                    @endif
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-forest-800">{{ $product->name }}</h1>

                <!-- Price and Stock -->
                <div class="mt-4 flex items-baseline gap-4 pb-4 border-b border-slate-100">
                    <span class="text-3xl font-black text-forest-800">₹{{ number_format((float) $product->price, 2) }}</span>
                    @if($product->unit || $product->weight)
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                            Per {{ $product->unit ?? 'packet' }} {{ $product->weight ? '('.$product->weight.')' : '' }}
                        </span>
                    @endif

                    <div class="ml-auto">
                        @if($product->isInStock())
                            @if($product->isLowStock())
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    ⚠️ Only {{ $product->stock_quantity }} units left in stock!
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ In Stock ({{ $product->stock_quantity }} available)
                                </span>
                            @endif
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                Currently Out of Stock
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="py-5 text-sm text-slate-600 leading-relaxed space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">About this variety</h3>
                    <p>{{ $product->description ?: 'Collected and preserved by our traditional seed conservators. Grown naturally and organically without synthetic chemicals.' }}</p>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <div class="pt-6 border-t border-slate-100">
                @if($product->isInStock())
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="flex items-center gap-4">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Quantity:</label>
                            <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden" x-data="{ qty: 1, max: {{ $product->stock_quantity }} }">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-3 py-2 text-slate-600 font-bold hover:bg-slate-200 transition">-</button>
                                <input type="number" name="quantity" x-model="qty" min="1" max="{{ $product->stock_quantity }}"
                                       class="w-14 text-center text-sm font-bold bg-transparent border-0 focus:ring-0 focus:outline-none">
                                <button type="button" @click="qty = Math.min(max, qty + 1)" class="px-3 py-2 text-slate-600 font-bold hover:bg-slate-200 transition">+</button>
                            </div>
                            <span class="text-xs text-slate-400">Max {{ $product->stock_quantity }} units</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <button type="submit" class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold py-3.5 px-6 rounded-2xl shadow transition text-sm flex items-center justify-center space-x-2">
                                <span>🛒 Add to Cart</span>
                            </button>
                            <button type="submit" name="buy_now" value="1" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow transition text-sm flex items-center justify-center space-x-2">
                                <span>⚡ Buy Now</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center">
                        <p class="text-xs text-slate-500 font-medium">This seed variety is currently out of stock. New harvest listings will be updated soon.</p>
                        <a href="{{ route('contact', ['subject' => 'Restock enquiry: '.$product->name]) }}" class="inline-block mt-2 text-xs font-bold text-sky-700 hover:underline">
                            Request restock notification &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-14">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">More Traditional Seeds & Kits</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-3xl border border-sky-100 p-5 shadow-sm hover:shadow-md transition">
                        <a href="{{ route('shop.show', $related) }}" class="block aspect-[4/3] rounded-2xl bg-sky-50 overflow-hidden mb-3">
                            @if($related->photoUrl())
                                <img src="{{ $related->photoUrl() }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-4xl">🌱</div>
                            @endif
                        </a>
                        <h3 class="text-sm font-bold text-slate-900 truncate">
                            <a href="{{ route('shop.show', $related) }}" class="hover:text-sky-700">{{ $related->name }}</a>
                        </h3>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                            <span class="text-sm font-bold text-forest-800">₹{{ number_format((float) $related->price, 2) }}</span>
                            <a href="{{ route('shop.show', $related) }}" class="text-xs font-bold text-sky-700 hover:underline">View &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
