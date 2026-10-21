@extends('layouts.app')

@section('title', 'Shopping Cart — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <div class="mb-8">
        <span class="inline-flex rounded-full bg-sky-50 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-sky-800">ਬੀਜਾਂ ਦੀ ਟੋਕਰੀ • Your Seed Basket</span>
        <h1 class="text-3xl md:text-4xl font-extrabold text-forest-800 mt-2">Shopping Cart</h1>
        <p class="text-xs text-slate-500 mt-1">Review your selected traditional seeds and seed kits before proceeding to checkout.</p>
    </div>

    @if(!empty($syncNotices))
        <div class="mb-6 space-y-2">
            @foreach($syncNotices as $notice)
                <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-3 rounded text-xs flex items-center justify-between">
                    <span>⚠️ {{ $notice }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if(count($items) > 0)
        <!-- Free Shipping Progress Indicator -->
        <div class="bg-white rounded-3xl border border-sky-100 p-6 shadow-sm mb-8">
            <div class="flex items-center justify-between text-xs mb-2">
                @if($amountNeededForFreeShipping <= 0)
                    <span class="font-bold text-emerald-700 flex items-center space-x-1">
                        <span>🎉</span>
                        <span>Congratulations! Your order qualifies for <strong>FREE Shipping across India!</strong></span>
                    </span>
                @else
                    <span class="text-slate-600">
                        Add <strong>₹{{ number_format($amountNeededForFreeShipping, 2) }}</strong> more of seeds to unlock <strong>FREE Shipping</strong>!
                    </span>
                @endif
                <span class="font-bold text-slate-700">₹{{ number_format($subtotal, 2) }} / ₹{{ number_format($freeShippingThreshold, 2) }}</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-500"
                     style="width: {{ min(100, ($subtotal / $freeShippingThreshold) * 100) }}%"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items Table (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900">Cart Items ({{ $count }})</h2>
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Empty your cart?')">
                            @csrf
                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold hover:underline">
                                Clear Cart
                            </button>
                        </form>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($items as $id => $item)
                            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-3xl flex-shrink-0 overflow-hidden border border-sky-100">
                                        @if(!empty($item['image_url']))
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            🌱
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">{{ $item['name'] }}</h3>
                                        <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-0.5">
                                            @if(!empty($item['sku']))
                                                <span class="font-mono text-slate-400">SKU: {{ $item['sku'] }}</span>
                                                <span>•</span>
                                            @endif
                                            <span>Per {{ $item['unit'] ?? 'packet' }}</span>
                                        </div>
                                        <p class="text-xs font-bold text-forest-800 mt-1">₹{{ number_format((float) $item['price'], 2) }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-6">
                                    <!-- Quantity adjustment form -->
                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden"
                                             x-data="{ qty: {{ $item['quantity'] }}, max: {{ $item['stock'] }} }">
                                            <button type="button" @click="if(qty > 1) { qty--; $nextTick(() => $el.form.submit()); }"
                                                    class="px-2.5 py-1.5 text-slate-600 font-bold hover:bg-slate-200">-</button>
                                            <input type="number" name="quantity" x-model="qty"
                                                   @change="$el.form.submit()"
                                                   min="1" max="{{ $item['stock'] }}"
                                                   class="w-12 text-center text-xs font-bold bg-transparent border-0 focus:ring-0 focus:outline-none">
                                            <button type="button" @click="if(qty < max) { qty++; $nextTick(() => $el.form.submit()); }"
                                                    class="px-2.5 py-1.5 text-slate-600 font-bold hover:bg-slate-200">+</button>
                                        </div>
                                    </form>

                                    <!-- Item Subtotal -->
                                    <div class="text-right min-w-20">
                                        <span class="text-sm font-black text-forest-800">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                    </div>

                                    <!-- Remove Button -->
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Remove item" class="text-slate-400 hover:text-rose-600 transition p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-2">
                    <a href="{{ route('shop.index') }}" class="font-bold text-sky-700 hover:text-sky-800 flex items-center space-x-1">
                        <span>&larr;</span>
                        <span>Continue Shopping Traditional Seeds</span>
                    </a>
                </div>
            </div>

            <!-- Order Summary Card (1 col) -->
            <div>
                <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm space-y-5 sticky top-24">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-4">Order Summary</h2>

                    <div class="space-y-3 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal ({{ $count }} {{ Str::plural('item', $count) }})</span>
                            <span class="font-semibold text-slate-900">₹{{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span>Delivery & Handling</span>
                            @if($shippingCost == 0)
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">FREE</span>
                            @else
                                <span class="font-semibold text-slate-900">₹{{ number_format($shippingCost, 2) }}</span>
                            @endif
                        </div>

                        <div class="border-t border-slate-100 pt-3 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-slate-900">Total Amount</span>
                            <span class="text-2xl font-black text-forest-800">₹{{ number_format($total, 2) }}</span>
                        </div>
                        <p class="text-[11px] text-slate-400">All prices inclusive of agricultural taxes and packaging.</p>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold py-4 rounded-2xl shadow transition text-sm flex items-center justify-center space-x-2">
                        <span>Proceed to Checkout</span>
                        <span>&rarr;</span>
                    </a>

                    <!-- Security trust points -->
                    <div class="border-t border-slate-100 pt-4 space-y-2 text-[11px] text-slate-500">
                        <div class="flex items-center space-x-2">
                            <span>🌱</span>
                            <span>Handpicked organic non-GMO heirloom seeds</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span>💵</span>
                            <span>Cash on Delivery & UPI payments accepted</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span>🛡️</span>
                            <span>Guaranteed safe arrival in tamper-proof bags</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart State -->
        <div class="bg-white rounded-3xl border border-dashed border-sky-200 p-16 text-center max-w-2xl mx-auto space-y-4">
            <div class="text-6xl">🧺</div>
            <h2 class="text-2xl font-bold text-slate-800">Your basket is currently empty</h2>
            <p class="text-sm text-slate-500 max-w-md mx-auto">
                Explore our documented collection of traditional wheat, millets, mustard, pulses, and kitchen garden seed kits.
            </p>
            <div class="pt-4">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center space-x-2 bg-sky-700 hover:bg-sky-800 text-white font-bold px-6 py-3.5 rounded-2xl text-sm shadow transition">
                    <span>Explore Seeds & Starter Kits</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
