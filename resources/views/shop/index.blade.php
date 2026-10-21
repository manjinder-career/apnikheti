@extends('layouts.app')

@section('title', 'Traditional Seeds & Seed Kits Shop — Apni Kheti Apne Beej')

@section('content')
<section class="bg-gradient-to-br from-sky-100 via-white to-emerald-50 border-b border-sky-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex rounded-full bg-white px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-sky-800 shadow-sm">
                    🌱 ਸੰਭਾਲੇ ਹੋਏ ਦੇਸੀ ਬੀਜ • 100% Traditional Native Seeds
                </span>
                <h1 class="mt-4 max-w-2xl text-3xl md:text-5xl font-extrabold tracking-tight text-forest-800">
                    Traditional Seeds & Starter Kits
                </h1>
                <p class="mt-3 max-w-xl text-sm md:text-base text-slate-600 leading-relaxed">
                    Direct from organic seed conservationists in Punjab. Free delivery on all orders above ₹500 across India.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="inline-flex items-center space-x-2 bg-white text-sky-800 border border-sky-200 px-4 py-3 rounded-2xl text-xs font-bold shadow-sm hover:shadow transition">
                    <span>🛒 View Cart</span>
                </a>
                <a href="{{ route('orders.track') }}" class="inline-flex items-center space-x-2 bg-sky-700 hover:bg-sky-800 text-white px-4 py-3 rounded-2xl text-xs font-bold shadow-sm transition">
                    <span>🚚 Track Order</span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Search & Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-sky-100 shadow-sm mb-8 space-y-4">
        <form action="{{ route('shop.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search seeds by name, variety, or SKU..."
                       class="w-full text-xs pl-10 pr-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:border-sky-600">
                <span class="absolute left-3.5 top-3.5 text-slate-400">🔍</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select name="type" class="text-xs px-3 py-3 rounded-2xl border border-slate-200 bg-white focus:outline-none focus:border-sky-600">
                    <option value="">All Types (Seeds & Kits)</option>
                    <option value="seed" {{ request('type') === 'seed' ? 'selected' : '' }}>Seeds Only</option>
                    <option value="kit" {{ request('type') === 'kit' ? 'selected' : '' }}>Starter Kits Only</option>
                </select>

                <select name="sort" class="text-xs px-3 py-3 rounded-2xl border border-slate-200 bg-white focus:outline-none focus:border-sky-600">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Harvest</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A-Z</option>
                </select>

                <label class="flex items-center space-x-2 text-xs text-slate-600 bg-slate-50 px-3 py-3 rounded-2xl border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="in_stock_only" value="1" {{ request('in_stock_only') ? 'checked' : '' }} class="rounded text-sky-700">
                    <span>In-Stock Only</span>
                </label>

                <button type="submit" class="bg-sky-700 hover:bg-sky-800 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow transition">
                    Filter
                </button>

                @if(request()->hasAny(['q', 'category', 'type', 'sort', 'in_stock_only']))
                    <a href="{{ route('shop.index') }}" class="text-xs text-slate-500 hover:text-slate-800 px-2 py-3">Reset</a>
                @endif
            </div>
        </form>

        <!-- Category Tags -->
        @if($categories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 mr-2">Categories:</span>
                <a href="{{ route('shop.index', array_merge(request()->except(['category', 'page']))) }}"
                   class="rounded-full px-3.5 py-1.5 text-xs font-bold transition {{ !request('category') ? 'bg-sky-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('shop.index', array_merge(request()->except(['page']), ['category' => $category->slug])) }}"
                       class="rounded-full px-3.5 py-1.5 text-xs font-bold transition {{ request('category') === $category->slug ? 'bg-sky-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $category->name }} ({{ $category->products_count }})
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <article class="group overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl flex flex-col justify-between">
                <div>
                    <!-- Product Image -->
                    <a href="{{ route('shop.show', $product) }}" class="block aspect-[4/3] bg-gradient-to-br from-sky-100 to-emerald-50 overflow-hidden relative">
                        @if($product->photoUrl())
                            <img src="{{ $product->photoUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="h-full flex items-center justify-center text-5xl" aria-hidden="true">{{ $product->product_type === 'kit' ? '🧺' : '🌱' }}</div>
                        @endif

                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            <span class="rounded-full bg-white/90 backdrop-blur px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-sky-800 shadow-sm">
                                {{ $product->product_type === 'kit' ? 'Seed kit' : 'Seed' }}
                            </span>
                            @if($product->unit)
                                <span class="rounded-full bg-slate-900/80 backdrop-blur px-2.5 py-1 text-[10px] font-semibold text-white shadow-sm">
                                    {{ $product->unit }}
                                </span>
                            @endif
                        </div>

                        <div class="absolute top-3 right-3">
                            @if($product->isInStock())
                                @if($product->isLowStock())
                                    <span class="rounded-full bg-amber-500 text-white px-2.5 py-1 text-[10px] font-bold shadow-sm">
                                        Only {{ $product->stock_quantity }} left!
                                    </span>
                                @else
                                    <span class="rounded-full bg-emerald-600 text-white px-2.5 py-1 text-[10px] font-bold shadow-sm">
                                        In Stock
                                    </span>
                                @endif
                            @else
                                <span class="rounded-full bg-rose-600 text-white px-2.5 py-1 text-[10px] font-bold shadow-sm">
                                    Out of stock
                                </span>
                            @endif
                        </div>
                    </a>

                    <div class="p-5">
                        <div class="flex items-center justify-between text-[11px] text-slate-500">
                            @if($product->category)
                                <span class="text-emerald-700 font-semibold">{{ $product->category->name }}</span>
                            @else
                                <span>Native Variety</span>
                            @endif
                            @if($product->sku)
                                <span class="font-mono text-slate-400">SKU: {{ $product->sku }}</span>
                            @endif
                        </div>

                        <h3 class="mt-2 text-base font-bold text-slate-900 line-clamp-1">
                            <a href="{{ route('shop.show', $product) }}" class="hover:text-sky-700 transition">
                                {{ $product->name }}
                            </a>
                        </h3>

                        @if($product->description)
                            <p class="mt-1 text-xs leading-relaxed text-slate-600 line-clamp-2">{{ $product->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="p-5 pt-0 border-t border-slate-100 mt-2">
                    <div class="flex items-center justify-between my-3">
                        <div>
                            <span class="text-xs text-slate-400 block">Price</span>
                            <span class="text-xl font-black text-forest-800">₹{{ number_format((float) $product->price, 2) }}</span>
                        </div>
                        @if($product->weight)
                            <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">{{ $product->weight }}</span>
                        @endif
                    </div>

                    @if($product->isInStock())
                        <div class="flex items-center gap-2">
                            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full rounded-xl bg-sky-700 hover:bg-sky-800 py-2.5 px-3 text-xs font-bold text-white shadow transition flex items-center justify-center space-x-1.5">
                                    <span>🛒</span>
                                    <span>Add to Cart</span>
                                </button>
                            </form>

                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" title="Buy Now" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 py-2.5 px-3 text-xs font-bold text-white shadow transition">
                                    ⚡ Buy Now
                                </button>
                            </form>
                        </div>
                    @else
                        <button disabled class="w-full rounded-xl bg-slate-100 py-2.5 text-xs font-semibold text-slate-400 cursor-not-allowed text-center">
                            Out of Stock
                        </button>
                    @endif
                </div>
            </article>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4 rounded-3xl border border-dashed border-sky-200 bg-white p-16 text-center">
                <div class="text-5xl" aria-hidden="true">🌾</div>
                <h3 class="mt-4 text-lg font-bold text-slate-800">No products found</h3>
                <p class="mt-2 text-sm text-slate-500">We could not find any products matching your search criteria.</p>
                <a href="{{ route('shop.index') }}" class="mt-5 inline-flex rounded-xl bg-sky-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-sky-800">
                    Clear Filters
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-10">{{ $products->links() }}</div>
</section>
@endsection
