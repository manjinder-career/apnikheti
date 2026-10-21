@extends('layouts.app')

@section('title', 'My Orders — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('account.dashboard') }}" class="hover:underline">Account</a>
                <span>&rsaquo;</span>
                <span class="font-bold text-slate-700">My Orders</span>
            </div>
            <h1 class="text-3xl font-bold text-forest-800">Your Order History</h1>
            <p class="text-xs text-slate-500 mt-1">Review traditional seed shipments, tracking codes, and invoices.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="self-start md:self-auto bg-sky-700 hover:bg-sky-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow flex items-center space-x-2">
            <span>+ Order More Seeds</span>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('account.orders.index') }}"
           class="px-4 py-2 rounded-full text-xs font-bold transition {{ !request('status') ? 'bg-sky-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            All Orders
        </a>
        <a href="{{ route('account.orders.index', ['status' => 'pending']) }}"
           class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('status') === 'pending' ? 'bg-sky-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Pending
        </a>
        <a href="{{ route('account.orders.index', ['status' => 'processing']) }}"
           class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('status') === 'processing' ? 'bg-sky-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Processing
        </a>
        <a href="{{ route('account.orders.index', ['status' => 'shipped']) }}"
           class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('status') === 'shipped' ? 'bg-sky-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Shipped
        </a>
        <a href="{{ route('account.orders.index', ['status' => 'delivered']) }}"
           class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('status') === 'delivered' ? 'bg-sky-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Delivered
        </a>
        <a href="{{ route('account.orders.index', ['status' => 'cancelled']) }}"
           class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('status') === 'cancelled' ? 'bg-sky-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Cancelled
        </a>
    </div>

    <!-- Orders Cards -->
    <div class="space-y-4">
        @forelse($orders as $order)
            <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-mono font-bold text-slate-900 bg-slate-100 px-3 py-1.5 rounded-lg">{{ $order->order_number }}</span>
                        <span class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y • h:i A') }}</span>
                        <span class="text-xs px-3 py-1 rounded-full font-bold border {{ $order->statusBadgeClasses() }}">
                            {{ $order->statusLabel() }}
                        </span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium border {{ $order->paymentStatusBadgeClasses() }}">
                            Payment: {{ ucfirst($order->payment_status) }} ({{ $order->paymentMethodLabel() }})
                        </span>
                    </div>

                    <div class="flex items-center gap-4 self-end md:self-auto">
                        <div class="text-right">
                            <span class="text-xs text-slate-400 block">Total Amount</span>
                            <span class="text-xl font-bold text-forest-800">₹{{ number_format((float) $order->total_amount, 2) }}</span>
                        </div>
                        <a href="{{ route('account.orders.show', $order->order_number) }}"
                           class="bg-sky-700 hover:bg-sky-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition">
                            View & Track &rarr;
                        </a>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-2xl">
                                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center font-bold text-slate-400 border border-slate-100 text-lg flex-shrink-0">
                                    🌱
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ $item->product_name }}</h4>
                                    <p class="text-[11px] text-slate-500">Qty: {{ $item->quantity }} × ₹{{ number_format((float) $item->unit_price, 2) }}</p>
                                </div>
                                <span class="text-xs font-bold text-slate-700">₹{{ number_format((float) $item->total_price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($order->tracking_number)
                    <div class="mt-5 bg-indigo-50/70 border border-indigo-100 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3 text-xs text-indigo-900">
                        <div class="flex items-center space-x-2">
                            <span>🚚</span>
                            <span>Carrier: <strong>{{ $order->courier_name ?? 'Postal Courier' }}</strong></span>
                            <span>•</span>
                            <span>Tracking #: <strong class="font-mono">{{ $order->tracking_number }}</strong></span>
                        </div>
                        <a href="{{ route('orders.track', ['order_number' => $order->order_number]) }}" class="font-bold underline text-indigo-700 hover:text-indigo-900">
                            Track Shipment Progress &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-dashed border-sky-200 p-12 text-center">
                <div class="text-5xl">📦</div>
                <h3 class="text-lg font-bold text-slate-800 mt-3">No orders found</h3>
                <p class="text-xs text-slate-500 mt-1">There are no orders matching your current filter.</p>
                <a href="{{ route('shop.index') }}" class="mt-4 inline-flex items-center space-x-2 bg-sky-700 hover:bg-sky-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition">
                    <span>Visit Seed Shop</span>
                    <span>&rarr;</span>
                </a>
            </div>
        @endforelse

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
