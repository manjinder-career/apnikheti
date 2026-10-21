@extends('layouts.app')

@section('title', 'Track Order — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <div class="text-center max-w-xl mx-auto mb-10">
        <span class="inline-flex rounded-full bg-sky-50 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-sky-800">
            ਆਰਡਰ ਟਰੈਕਿੰਗ • Live Tracking
        </span>
        <h1 class="text-3xl md:text-4xl font-extrabold text-forest-800 mt-2">Track Your Seed Shipment</h1>
        <p class="text-xs text-slate-500 mt-2">Enter your Order Number and the Email or Phone used during checkout to view the real-time dispatch progress.</p>
    </div>

    <!-- Search Box -->
    <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm mb-10">
        <form action="{{ route('orders.track') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Order Number <span class="text-red-500">*</span></label>
                    <input type="text" name="order_number" required value="{{ request('order_number') }}" placeholder="e.g. AK-20261021-XXXX"
                           class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 font-mono uppercase">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email or Phone Number @guest <span class="text-red-500">*</span> @endguest</label>
                    <input type="text" name="contact" @guest required @endguest value="{{ request('contact') }}" placeholder="e.g. you@example.com or +91 98765 43210"
                           class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                </div>
            </div>

            <button type="submit" class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold py-3.5 rounded-xl shadow transition text-xs flex items-center justify-center space-x-2">
                <span>🔍 Search Shipment Status</span>
            </button>
        </form>
    </div>

    @if($searched)
        @if($order)
            <!-- Order Found Result -->
            <div class="space-y-6">
                <!-- Stepper card -->
                <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-3">
                        <div>
                            <span class="text-xs text-slate-400 font-mono">Order Number</span>
                            <h2 class="text-xl font-bold text-slate-900 font-mono">{{ $order->order_number }}</h2>
                            <span class="text-xs text-slate-400">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</span>
                        </div>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border self-start sm:self-auto {{ $order->statusBadgeClasses() }}">
                            {{ $order->statusLabel() }}
                        </span>
                    </div>

                    <!-- Progress Stepper -->
                    @php
                        $steps = ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
                        $currentIndex = array_search($order->status, $steps);
                        $isCancelled = $order->status === 'cancelled';
                    @endphp

                    @if($isCancelled)
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 my-6 text-center text-rose-800 text-xs">
                            <span class="text-2xl block mb-1">❌</span>
                            <p class="font-bold">This order has been cancelled.</p>
                        </div>
                    @else
                        <div class="py-8 relative">
                            <div class="hidden sm:block absolute top-12 left-8 right-8 h-1 bg-slate-200 -z-0">
                                <div class="h-1 bg-forest-600 transition-all duration-500"
                                     style="width: {{ $currentIndex === false ? 0 : ($currentIndex / (count($steps) - 1)) * 100 }}%"></div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 relative z-10">
                                @php
                                    $stepLabels = [
                                        'pending' => ['name' => 'Order Placed', 'icon' => '📝'],
                                        'processing' => ['name' => 'Packed', 'icon' => '📦'],
                                        'shipped' => ['name' => 'Dispatched', 'icon' => '🚚'],
                                        'out_for_delivery' => ['name' => 'Out for Delivery', 'icon' => '🛵'],
                                        'delivered' => ['name' => 'Delivered', 'icon' => '✅'],
                                    ];
                                @endphp

                                @foreach($steps as $idx => $stepKey)
                                    @php
                                        $stepDone = $currentIndex !== false && $idx <= $currentIndex;
                                        $stepActive = $currentIndex !== false && $idx === $currentIndex;
                                    @endphp
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition
                                                    {{ $stepDone ? 'bg-forest-700 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200' }}
                                                    {{ $stepActive ? 'ring-4 ring-forest-100' : '' }}">
                                            <span>{{ $stepLabels[$stepKey]['icon'] }}</span>
                                        </div>
                                        <span class="mt-2 text-xs font-bold {{ $stepDone ? 'text-slate-900' : 'text-slate-400' }}">
                                            {{ $stepLabels[$stepKey]['name'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($order->tracking_number)
                            <div class="bg-sky-50 border border-sky-100 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4 text-xs">
                                <div>
                                    <span class="text-slate-500">Shipping Partner:</span>
                                    <strong class="text-slate-800 ml-1">{{ $order->courier_name ?? 'Postal Courier' }}</strong>
                                    <span class="mx-2">•</span>
                                    <span class="text-slate-500">Tracking Code:</span>
                                    <strong class="font-mono text-sky-800 ml-1">{{ $order->tracking_number }}</strong>
                                </div>
                                <span class="text-slate-500 italic">Expected in 3-5 business days</span>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Details & Timeline Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Items & Summary -->
                    <div class="bg-white rounded-3xl border border-sky-100 p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Package Contents</h3>
                        <div class="space-y-2 text-xs">
                            @foreach($order->items as $item)
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-700">🌱 {{ $item->product_name }} (×{{ $item->quantity }})</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format((float) $item->total_price, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="border-t border-slate-100 pt-2 flex justify-between font-bold text-slate-900 text-sm">
                                <span>Total (inc. shipping)</span>
                                <span class="text-forest-800">₹{{ number_format((float) $order->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <div class="pt-2 text-xs text-slate-500">
                            <strong>Delivering to:</strong>
                            <p class="mt-0.5 text-slate-700">{{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="bg-white rounded-3xl border border-sky-100 p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Tracking History</h3>
                        <div class="space-y-3 text-xs max-h-60 overflow-y-auto">
                            @forelse($order->statusHistories as $history)
                                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                    <div class="flex justify-between text-[11px]">
                                        <span class="font-bold text-slate-800 uppercase">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</span>
                                        <span class="text-slate-400">{{ $history->created_at->format('M d, Y • h:i A') }}</span>
                                    </div>
                                    @if($history->comment)
                                        <p class="text-slate-600 mt-1">{{ $history->comment }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-slate-400 italic">No tracking events recorded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Not Found State -->
            <div class="bg-white rounded-3xl border border-dashed border-rose-200 p-12 text-center max-w-md mx-auto space-y-3">
                <div class="text-4xl">🔎</div>
                <h3 class="text-base font-bold text-slate-800">No matching order found</h3>
                <p class="text-xs text-slate-500">
                    Please ensure the order number and email/phone match the details used during checkout. If you need assistance, please contact our team.
                </p>
                <div class="pt-2">
                    <a href="{{ route('contact') }}" class="text-xs font-bold text-sky-700 hover:underline">Contact Support &rarr;</a>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
