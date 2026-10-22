@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('admin_content')

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-xs text-gray-500 mt-0.5">Seed conservation database, educational resources, and e-commerce management.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.orders.index') }}" class="bg-forest-700 hover:bg-forest-800 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow">
                📦 Manage Orders
            </a>
            <a href="{{ route('admin.settings.index') }}#payment-settings" class="bg-white hover:bg-gray-50 text-gray-800 text-xs font-semibold px-4 py-2 rounded-lg border border-gray-300 shadow-sm">
                💳 Payment Settings
            </a>
            <a href="{{ route('admin.shop-products.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow">
                + Add Shop Product
            </a>
            <a href="{{ route('admin.seeds.create') }}" class="bg-sky-700 hover:bg-sky-800 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow">
                + Add Seed
            </a>
        </div>
    </div>

    <!-- E-Commerce KPI Cards -->
    <div>
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">E-Commerce Performance</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-emerald-100 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Paid Seed Revenue</span>
                <span class="block text-3xl font-black text-forest-700">₹{{ number_format((float) $totalRevenue, 2) }}</span>
                <span class="text-[11px] text-gray-500 inline-block pt-1">Across all confirmed customer orders</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-sky-100 shadow-sm space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold text-gray-400 uppercase">Customer Orders</span>
                    @if($pendingOrdersCount > 0)
                        <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-[10px] font-bold">
                            {{ $pendingOrdersCount }} Pending
                        </span>
                    @endif
                </div>
                <span class="block text-3xl font-black text-sky-800">{{ $totalOrdersCount }}</span>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-sky-700 hover:underline inline-block pt-1 font-semibold">View All Orders &rarr;</a>
            </div>

            <div class="bg-white p-5 rounded-xl border border-purple-100 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Registered Customers</span>
                <span class="block text-3xl font-black text-purple-700">{{ $totalCustomersCount }}</span>
                <a href="{{ route('admin.customers.index') }}" class="text-xs text-purple-700 hover:underline inline-block pt-1 font-semibold">Manage Customers &rarr;</a>
            </div>

            <div class="bg-white p-5 rounded-xl border {{ $lowStockCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-gray-200' }} shadow-sm space-y-1">
                <span class="text-xs font-semibold {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-400' }} uppercase">Low Stock Alerts (≤ 5)</span>
                <span class="block text-3xl font-black {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-700' }}">{{ $lowStockCount }}</span>
                <a href="{{ route('admin.shop-products.index', ['stock_status' => 'low_stock']) }}" class="text-xs text-amber-800 hover:underline inline-block pt-1 font-semibold">Review Inventory &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Seed Initiative & Content Metrics -->
    <div>
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Initiative & Content Archive</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border shadow-sm space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Documented Seeds</span>
                <span class="block text-2xl font-black text-forest-700">{{ $publishedSeedsCount }}</span>
                <a href="{{ route('admin.seeds.index') }}" class="text-xs text-emerald-700 hover:underline inline-block font-medium">Seed Library &rarr;</a>
            </div>

            <div class="bg-white p-4 rounded-xl border shadow-sm space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Pending Submissions</span>
                <span class="block text-2xl font-black text-amber-600">{{ $pendingSubmissionsCount }}</span>
                <a href="{{ route('admin.submissions.index') }}" class="text-xs text-amber-700 hover:underline inline-block font-medium">Review &rarr;</a>
            </div>

            <div class="bg-white p-4 rounded-xl border shadow-sm space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Educational Articles</span>
                <span class="block text-2xl font-black text-emerald-700">{{ $publishedArticlesCount }}</span>
                <a href="{{ route('admin.articles.index') }}" class="text-xs text-emerald-700 hover:underline inline-block font-medium">Articles &rarr;</a>
            </div>

            <div class="bg-white p-4 rounded-xl border shadow-sm space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Pending Farmer Stories</span>
                <span class="block text-2xl font-black text-blue-600">{{ $pendingStoriesCount }}</span>
                <a href="{{ route('admin.farmer-stories.index') }}" class="text-xs text-blue-700 hover:underline inline-block font-medium">Stories &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Recent Customer Orders -->
    <div class="bg-white rounded-xl border shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-base font-bold text-gray-800">Recent Customer Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-emerald-700 hover:underline font-semibold">View All Orders &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 uppercase text-gray-500 border-b text-[10px]">
                    <tr>
                        <th class="p-3">Order Number</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Items</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-mono font-bold">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="p-3 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="p-3">
                                <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>
                                <span class="block text-[11px] text-gray-400">{{ $order->shipping_city }}</span>
                            </td>
                            <td class="p-3 text-gray-600">{{ $order->items->count() }} items</td>
                            <td class="p-3 font-bold text-gray-900">₹{{ number_format((float) $order->total_amount, 2) }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $order->statusBadgeClasses() }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="bg-forest-700 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-forest-800">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-400 italic">No orders placed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Pending Submissions Table -->
    <div class="bg-white rounded-xl border shadow-sm p-6 space-y-4">
        <h2 class="text-base font-bold text-gray-800">Pending Seed Submissions for Review</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 uppercase text-gray-500 border-b text-[10px]">
                    <tr>
                        <th class="p-3">Contributor</th>
                        <th class="p-3">Seed Name (Punjabi)</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Date</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentSubmissions as $sub)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-semibold">{{ $sub->contributor_name }} ({{ $sub->district }})</td>
                            <td class="p-3 font-gurmukhi text-sm font-bold text-forest-800">{{ $sub->seed_name_pa }}</td>
                            <td class="p-3">{{ $sub->crop_category_name ?? 'N/A' }}</td>
                            <td class="p-3 text-gray-500">{{ $sub->created_at->format('M d, Y') }}</td>
                            <td class="p-3 text-right">
                                <a href="{{ route('admin.submissions.show', $sub->id) }}" class="bg-amber-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-amber-700">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">No pending submissions awaiting review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
