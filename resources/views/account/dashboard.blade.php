@extends('layouts.app')

@section('title', 'My Account — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <!-- Header banner -->
    <div class="bg-gradient-to-r from-sky-800 via-sky-700 to-forest-800 rounded-3xl p-8 md:p-10 text-white shadow-xl relative overflow-hidden mb-10">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">Customer Portal</span>
                <h1 class="text-3xl md:text-4xl font-bold mt-2">Welcome back, {{ $user->name }}!</h1>
                <p class="text-sky-100 text-sm mt-1">{{ $user->email }} • Member since {{ $user->created_at->format('M Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('shop.index') }}" class="bg-white text-sky-800 hover:bg-sky-50 font-bold px-5 py-2.5 rounded-xl text-xs transition shadow">
                    🌱 Explore Seeds & Kits
                </a>
                <a href="{{ route('account.orders.index') }}" class="bg-sky-900/60 hover:bg-sky-900/80 text-white font-semibold px-5 py-2.5 rounded-xl text-xs transition border border-white/20">
                    📦 My Orders
                </a>
                <a href="{{ route('account.profile') }}" class="bg-sky-900/60 hover:bg-sky-900/80 text-white font-semibold px-5 py-2.5 rounded-xl text-xs transition border border-white/20">
                    ⚙️ Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Orders Placed</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalOrdersCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center text-2xl">📦</div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Active / Pending Deliveries</p>
                <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $pendingOrdersCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-2xl">🚚</div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Seeds Value</p>
                <p class="text-3xl font-extrabold text-forest-700 mt-2">₹{{ number_format($totalSpent, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl">🌱</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Recent Orders</h2>
                        <p class="text-xs text-slate-500">Track and view your recent seed kit and native variety orders.</p>
                    </div>
                    <a href="{{ route('account.orders.index') }}" class="text-xs font-bold text-sky-700 hover:text-sky-800">View All ({{ $totalOrdersCount }}) &rarr;</a>
                </div>

                @forelse($recentOrders as $order)
                    <div class="border border-slate-100 rounded-2xl p-5 mb-4 hover:border-sky-200 transition">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100">
                            <div>
                                <span class="text-xs text-slate-400 font-mono">Order #</span>
                                <span class="text-sm font-bold text-slate-900 font-mono">{{ $order->order_number }}</span>
                                <span class="text-xs text-slate-400 ml-2">• {{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $order->statusBadgeClasses() }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </div>
                        </div>

                        <div class="py-3 flex flex-wrap items-center justify-between gap-4">
                            <div class="text-xs text-slate-600">
                                <strong>{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}:</strong>
                                {{ $order->items->pluck('product_name')->take(2)->join(', ') }}
                                @if($order->items->count() > 2)
                                    <span class="text-slate-400">+{{ $order->items->count() - 2 }} more</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-base font-bold text-forest-800">₹{{ number_format((float) $order->total_amount, 2) }}</span>
                                <a href="{{ route('account.orders.show', $order->order_number) }}" class="rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 px-3.5 py-1.5 text-xs font-bold transition">
                                    View & Track &rarr;
                                </a>
                            </div>
                        </div>

                        @if($order->tracking_number)
                            <div class="bg-indigo-50/60 rounded-xl px-3 py-2 text-xs text-indigo-900 flex items-center justify-between">
                                <span>🚚 Shipped via <strong>{{ $order->courier_name ?? 'Courier' }}</strong></span>
                                <span class="font-mono font-semibold">{{ $order->tracking_number }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 border border-dashed border-sky-200 rounded-2xl bg-sky-50/30">
                        <div class="text-4xl">🌾</div>
                        <h3 class="text-sm font-bold text-slate-800 mt-2">No orders placed yet</h3>
                        <p class="text-xs text-slate-500 mt-1">Start growing traditional crops and preserving native biodiversity today.</p>
                        <a href="{{ route('shop.index') }}" class="mt-4 inline-flex items-center space-x-1 bg-sky-700 hover:bg-sky-800 text-white font-bold px-4 py-2 rounded-xl text-xs transition">
                            <span>Browse Seeds</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Saved Shipping Address & Quick Actions (1 col) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Default Shipping Address</h3>
                    <a href="{{ route('account.profile') }}" class="text-xs text-sky-700 font-bold hover:underline">Edit</a>
                </div>

                @if($user->address)
                    <div class="space-y-1 text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-2xl">
                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                        <p>{{ $user->address }}</p>
                        <p>{{ $user->city }}, {{ $user->state }} — {{ $user->pincode }}</p>
                        <p class="pt-2 text-slate-500">📞 {{ $user->phone }}</p>
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-800 space-y-2">
                        <p class="font-semibold">No default address saved yet.</p>
                        <p>Save your address for faster 1-click checkout on your seed kit orders.</p>
                        <a href="{{ route('account.profile') }}" class="inline-block font-bold text-amber-900 underline mt-1">Add Address Now</a>
                    </div>
                @endif
            </div>

            <!-- Quick Track Order Widget -->
            <div class="bg-gradient-to-br from-emerald-50 to-sky-50 rounded-3xl border border-emerald-100 p-6 space-y-3">
                <div class="text-2xl">🔍</div>
                <h3 class="text-base font-bold text-forest-800">Track Any Order</h3>
                <p class="text-xs text-slate-600">Have an order tracking number from SMS or receipt?</p>
                <form action="{{ route('orders.track') }}" method="GET" class="space-y-2">
                    <input type="text" name="order_number" required placeholder="e.g. AK-20261021-XXXX"
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-sky-600">
                    <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-2.5 rounded-xl text-xs shadow transition">
                        Track Status &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
