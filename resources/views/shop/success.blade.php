@extends('layouts.app')

@section('title', 'Order Confirmed — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="bg-white rounded-3xl border border-sky-100 p-8 md:p-12 shadow-xl text-center space-y-6">
        <!-- Success Check Icon -->
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-4xl mx-auto shadow-inner">
            ✓
        </div>

        <div>
            <span class="inline-flex rounded-full bg-emerald-50 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-emerald-800">
                ਧੰਨਵਾਦ • Order Placed Successfully
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-forest-800 mt-2">Thank You for Preserving Traditional Seeds!</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-2 max-w-lg mx-auto">
                We have received your order. Our seed conservation team will carefully inspect, pack, and dispatch your seed varieties.
            </p>
        </div>

        <!-- Order Number Badge -->
        <div class="bg-sky-50 border border-sky-100 rounded-2xl p-4 max-w-md mx-auto text-xs space-y-1">
            <span class="text-slate-400 uppercase tracking-wider block font-bold text-[10px]">Your Order Number</span>
            <span class="text-xl font-black text-sky-800 font-mono tracking-wide">{{ $order->order_number }}</span>
            <p class="text-[11px] text-slate-500 pt-1">Confirmation sent to <strong>{{ $order->customer_email }}</strong></p>
        </div>

        <!-- Quick Summary Box -->
        <div class="bg-slate-50 rounded-2xl p-6 text-left max-w-2xl mx-auto space-y-4 text-xs">
            <h3 class="font-bold text-slate-900 border-b border-slate-200 pb-2 text-sm">Order Overview</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="text-slate-400 block font-semibold">Delivery Address</span>
                    <p class="font-bold text-slate-800 mt-0.5">{{ $order->customer_name }}</p>
                    <p class="text-slate-600">{{ $order->shipping_address }}</p>
                    <p class="text-slate-600">{{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_pincode }}</p>
                    <p class="text-slate-500 mt-1">📞 {{ $order->customer_phone }}</p>
                </div>

                <div>
                    <span class="text-slate-400 block font-semibold">Payment Details</span>
                    <p class="font-bold text-slate-800 mt-0.5">{{ $order->paymentMethodLabel() }}</p>
                    <p class="text-slate-600">Total: <strong>₹{{ number_format((float) $order->total_amount, 2) }}</strong></p>
                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full font-bold border {{ $order->paymentStatusBadgeClasses() }}">
                        Status: {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>

            <!-- Items -->
            <div class="border-t border-slate-200 pt-3">
                <span class="text-slate-400 block font-semibold mb-2">Seed Varieties Ordered ({{ $order->items->count() }})</span>
                <div class="space-y-1.5">
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-700">🌱 {{ $item->product_name }} (×{{ $item->quantity }})</span>
                            <span class="font-bold text-slate-800">₹{{ number_format((float) $item->total_price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- UPI Payment Box with Scanner & Transaction Submission -->
        <div class="max-w-2xl mx-auto pt-2">
            @include('shop.partials.upi-payment-box', ['order' => $order])
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
            <a href="{{ route('orders.track', ['order_number' => $order->order_number, 'contact' => $order->customer_email]) }}"
               class="bg-sky-700 hover:bg-sky-800 text-white font-bold px-6 py-3.5 rounded-xl text-xs shadow transition flex items-center space-x-2">
                <span>🔍 Track Order Status</span>
            </a>

            @auth
                <a href="{{ route('account.orders.show', $order->order_number) }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold px-6 py-3.5 rounded-xl text-xs transition">
                    View in Customer Portal
                </a>
            @endauth

            <a href="{{ route('shop.index') }}"
               class="text-sky-700 hover:text-sky-800 font-bold px-4 py-3.5 text-xs">
                Continue Browsing Seeds &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
