@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <!-- Breadcrumb & Title -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('account.dashboard') }}" class="hover:underline">Account</a>
                <span>&rsaquo;</span>
                <a href="{{ route('account.orders.index') }}" class="hover:underline">Orders</a>
                <span>&rsaquo;</span>
                <span class="font-bold text-slate-700 font-mono">{{ $order->order_number }}</span>
            </div>
            <h1 class="text-3xl font-bold text-forest-800 flex items-center gap-3">
                <span>Order #{{ $order->order_number }}</span>
                <span class="text-xs px-3 py-1 rounded-full font-bold border {{ $order->statusBadgeClasses() }}">
                    {{ $order->statusLabel() }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('orders.track', ['order_number' => $order->order_number, 'contact' => $order->customer_email]) }}"
               class="bg-sky-50 text-sky-700 hover:bg-sky-100 font-bold px-4 py-2.5 rounded-xl text-xs transition">
                🔍 Live Tracking Page
            </a>

            @if($order->canCancel())
                <form action="{{ route('account.orders.cancel', $order->order_number) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to cancel this order? Stock will be released.')">
                    @csrf
                    <button type="submit" class="bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold px-4 py-2.5 rounded-xl text-xs transition">
                        Cancel Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Tracking Stepper Component -->
    <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm mb-8">
        <h2 class="text-base font-bold text-slate-900 mb-6">Delivery Progress</h2>

        @php
            $steps = ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
            $currentIndex = array_search($order->status, $steps);
            $isCancelled = $order->status === 'cancelled';
        @endphp

        @if($isCancelled)
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 text-center text-rose-800">
                <span class="text-2xl block mb-1">❌</span>
                <p class="font-bold text-sm">This order has been cancelled.</p>
                <p class="text-xs text-rose-600 mt-0.5">If you have any questions or would like to re-order, please contact our seed coordinator team.</p>
            </div>
        @else
            <div class="relative">
                <div class="hidden sm:block absolute top-5 left-8 right-8 h-1 bg-slate-200 -z-0">
                    <div class="h-1 bg-forest-600 transition-all duration-500"
                         style="width: {{ $currentIndex === false ? 0 : ($currentIndex / (count($steps) - 1)) * 100 }}%"></div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 relative z-10">
                    @php
                        $stepLabels = [
                            'pending' => ['name' => 'Order Placed', 'icon' => '📝'],
                            'processing' => ['name' => 'Processing & Packed', 'icon' => '📦'],
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
                <div class="mt-8 bg-sky-50 border border-sky-100 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4 text-xs">
                    <div>
                        <span class="text-slate-500">Shipping Carrier:</span>
                        <strong class="text-slate-800 ml-1">{{ $order->courier_name ?? 'Speed Post / Courier' }}</strong>
                        <span class="mx-2">•</span>
                        <span class="text-slate-500">Tracking Code:</span>
                        <strong class="font-mono text-sky-800 ml-1">{{ $order->tracking_number }}</strong>
                    </div>
                    <span class="text-slate-500 italic">Please allow 24-48 hours for courier tracking events to sync.</span>
                </div>
            @endif
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Items in this order (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Items Ordered ({{ $order->items->count() }})</h3>

                <div class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <div class="py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center text-2xl flex-shrink-0 border border-sky-100">
                                    @if($item->product && $item->product->photoUrl())
                                        <img src="{{ $item->product->photoUrl() }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover rounded-2xl">
                                    @else
                                        🌱
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $item->product_name }}</h4>
                                    @if($item->product_sku)
                                        <p class="text-[11px] text-slate-400 font-mono">SKU: {{ $item->product_sku }}</p>
                                    @endif
                                    <p class="text-xs text-slate-500 mt-1">₹{{ number_format((float) $item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                </div>
                            </div>

                            <span class="text-base font-bold text-forest-800">₹{{ number_format((float) $item->total_price, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Order Totals summary -->
                <div class="border-t border-slate-100 mt-4 pt-4 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Items Subtotal</span>
                        <span>₹{{ number_format((float) $order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Shipping & Handling</span>
                        <span>{{ (float) $order->shipping_cost > 0 ? '₹'.number_format((float) $order->shipping_cost, 2) : 'FREE' }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-forest-800 pt-2 border-t border-slate-100">
                        <span>Total Amount</span>
                        <span>₹{{ number_format((float) $order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Tracking History Timeline -->
            @if($order->statusHistories->isNotEmpty())
                <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Status & Delivery Timeline</h3>
                    <div class="space-y-4 relative before:absolute before:inset-0 before:left-3.5 before:w-0.5 before:bg-slate-200">
                        @foreach($order->statusHistories as $history)
                            <div class="relative flex items-start space-x-3 text-xs">
                                <div class="w-7 h-7 rounded-full bg-forest-600 text-white flex items-center justify-center font-bold text-[10px] z-10 flex-shrink-0">
                                    ✓
                                </div>
                                <div class="bg-slate-50 p-3 rounded-2xl flex-1 border border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $history->created_at->format('M d, Y • h:i A') }}</span>
                                    </div>
                                    @if($history->comment)
                                        <p class="text-slate-600 mt-1">{{ $history->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Order Information & Shipping details (1 col) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-sky-100 p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900">Shipping Details</h3>
                <div class="text-xs text-slate-600 space-y-1 bg-slate-50 p-4 rounded-2xl">
                    <p class="font-bold text-slate-900">{{ $order->customer_name }}</p>
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_pincode }}</p>
                    <p class="pt-2 text-slate-500">📞 {{ $order->customer_phone }}</p>
                    <p class="text-slate-500">✉️ {{ $order->customer_email }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-sky-100 p-6 shadow-sm space-y-3">
                <h3 class="text-base font-bold text-slate-900">Payment Information</h3>
                <div class="text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Method:</span>
                        <span class="font-semibold text-slate-800">{{ $order->paymentMethodLabel() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Payment Status:</span>
                        <span class="px-2.5 py-0.5 rounded-full font-bold border {{ $order->paymentStatusBadgeClasses() }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            @if($order->customer_notes)
                <div class="bg-white rounded-3xl border border-sky-100 p-6 shadow-sm space-y-2">
                    <h3 class="text-base font-bold text-slate-900">Delivery Instructions</h3>
                    <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl italic">"{{ $order->customer_notes }}"</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
