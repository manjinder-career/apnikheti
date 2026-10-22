@extends('layouts.admin')

@section('title', 'Shop Products & Inventory')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Seed Products & Inventory</h1>
            <p class="mt-1 text-xs text-gray-500">Manage catalog, real-time stock levels, pricing, and availability.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.shop-categories.index') }}" class="inline-flex items-center bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm hover:bg-gray-50">
                🏷️ Manage Categories
            </a>
            <a href="{{ route('admin.shop-products.create') }}" class="inline-flex items-center bg-forest-700 hover:bg-forest-800 text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow">
                + Add New Product
            </a>
        </div>
    </div>

    <!-- Inventory Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase font-semibold">Total Listed Products</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $products->total() }}</p>
            </div>
            <span class="text-2xl">🌱</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-amber-700 uppercase font-semibold">Low Stock Alert (≤ 5)</p>
                <p class="text-2xl font-bold text-amber-700 mt-1">{{ $lowStockCount }}</p>
            </div>
            <span class="text-2xl">⚠️</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-rose-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-rose-700 uppercase font-semibold">Out of Stock</p>
                <p class="text-2xl font-bold text-rose-700 mt-1">{{ $outOfStockCount }}</p>
            </div>
            <span class="text-2xl">🛑</span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.shop-products.index') }}" method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by product name or SKU..."
                       class="w-full text-xs px-3 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
            </div>

            <select name="category_id" class="text-xs px-3 py-2.5 rounded-lg border border-gray-300 bg-white">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="stock_status" class="text-xs px-3 py-2.5 rounded-lg border border-gray-300 bg-white">
                <option value="">All Stock Levels</option>
                <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock (> 5)</option>
                <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock (≤ 5)</option>
                <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>

            <button type="submit" class="bg-forest-700 hover:bg-forest-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow">
                Filter
            </button>

            @if(request()->hasAny(['q', 'category_id', 'stock_status']))
                <a href="{{ route('admin.shop-products.index') }}" class="text-xs text-gray-500 hover:underline self-center">Clear</a>
            @endif
        </form>
    </div>

    <!-- Products Table -->
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                    <tr>
                        <th class="p-3">Product</th>
                        <th class="p-3">SKU</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Price</th>
                        <th class="p-3">Inventory / Stock</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50/70">
                            <td class="p-3 font-semibold text-gray-900">
                                <div class="flex items-center gap-3">
                                    @if($product->photoUrl())
                                        <img src="{{ $product->photoUrl() }}" alt="" class="h-10 w-12 rounded object-cover border flex-shrink-0">
                                    @else
                                        <div class="h-10 w-12 rounded bg-gray-100 flex items-center justify-center text-lg border flex-shrink-0">🌱</div>
                                    @endif
                                    <div>
                                        <span>{{ $product->name }}</span>
                                        <span class="block text-[11px] text-gray-400 capitalize">{{ $product->product_type }} • {{ $product->unit }} • ⚖️ {{ $product->formattedWeight() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 font-mono text-gray-600">{{ $product->sku ?? '-' }}</td>
                            <td class="p-3">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                            <td class="p-3 font-bold text-gray-900">₹{{ number_format((float) $product->price, 2) }}</td>
                            <td class="p-3">
                                @if($product->stock_quantity <= 0)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                        0 (Out of stock)
                                    </span>
                                @elseif($product->stock_quantity <= 5)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        {{ $product->stock_quantity }} left (Low)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $product->stock_quantity }} in stock
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $product->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $product->is_published ? 'Published' : 'Draft' }}
                                </span>
                                <span class="ml-1 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $product->is_available ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $product->is_available ? 'Available' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <a href="{{ route('shop.show', $product) }}" target="_blank" class="font-semibold text-gray-500 hover:text-gray-700 mr-2">View</a>
                                <a href="{{ route('admin.shop-products.edit', $product) }}" class="font-semibold text-emerald-700 hover:underline">Edit</a>
                                <form action="{{ route('admin.shop-products.destroy', $product) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this shop product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-gray-500">No shop products found matching your filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $products->links() }}
</div>
@endsection
