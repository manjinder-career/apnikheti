@extends('layouts.app')

@section('title', 'Checkout — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('shop.index') }}" class="hover:underline">Shop</a>
            <span>&rsaquo;</span>
            <a href="{{ route('cart.index') }}" class="hover:underline">Cart</a>
            <span>&rsaquo;</span>
            <span class="font-bold text-slate-700">Checkout</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-forest-800">Complete Your Order</h1>
        <p class="text-xs text-slate-500 mt-1">Provide your delivery details and choose a convenient payment option.</p>
    </div>

    @if($errors->has('cart'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-600 text-red-800 p-4 rounded-xl text-xs">
            <strong>Stock Notice:</strong> {{ $errors->first('cart') }}
            <a href="{{ route('cart.index') }}" class="underline font-bold ml-2">Return to Cart</a>
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Checkout Form Fields (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Customer Details & Shipping Address -->
                <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                            <span>1.</span>
                            <span>Customer & Delivery Details</span>
                        </h2>
                        @guest
                            <span class="text-xs text-slate-500">
                                Already registered? <a href="{{ route('login') }}" class="font-bold text-sky-700 hover:underline">Log in</a>
                            </span>
                        @endguest
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Recipient Name <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name', $user->name ?? '') }}" placeholder="Full Name"
                                   class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                            @error('customer_name')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="customer_phone" required value="{{ old('customer_phone', $user->phone ?? '') }}" placeholder="For delivery updates & SMS"
                                   class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                            @error('customer_phone')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="customer_email" required value="{{ old('customer_email', $user->email ?? '') }}" placeholder="Order receipt and tracking link sent here"
                               class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('customer_email')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Street Address / Village / Farm <span class="text-red-500">*</span></label>
                        <textarea name="shipping_address" required rows="2" placeholder="House / Plot / Farm No., Street, Village, Post Office"
                                  class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">{{ old('shipping_address', $user->address ?? '') }}</textarea>
                        @error('shipping_address')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">City / District <span class="text-red-500">*</span></label>
                            <input type="text" name="shipping_city" required value="{{ old('shipping_city', $user->city ?? '') }}" placeholder="e.g. Ludhiana"
                                   class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                            @error('shipping_city')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">State <span class="text-red-500">*</span></label>
                            <input type="text" name="shipping_state" required value="{{ old('shipping_state', $user->state ?? 'Punjab') }}" placeholder="e.g. Punjab"
                                   class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                            @error('shipping_state')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">PIN Code <span class="text-red-500">*</span></label>
                            <input type="text" name="shipping_pincode" required value="{{ old('shipping_pincode', $user->pincode ?? '') }}" placeholder="e.g. 141001"
                                   class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                            @error('shipping_pincode')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Optional Account Creation for Guests -->
                    @guest
                        <div x-data="{ createAccount: {{ old('create_account') ? 'true' : 'false' }} }" class="border-t border-slate-100 pt-4">
                            <label class="flex items-center space-x-2 text-xs font-semibold text-slate-800 cursor-pointer">
                                <input type="checkbox" name="create_account" value="1" x-model="createAccount" class="rounded text-sky-700">
                                <span>Create a customer account for instant order tracking & faster future checkouts</span>
                            </label>

                            <div x-show="createAccount" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-sky-50/50 p-4 rounded-2xl border border-sky-100">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Set Account Password</label>
                                    <input type="password" name="password" placeholder="Min. 8 characters"
                                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white">
                                    @error('password')
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                                    <input type="password" name="password_confirmation" placeholder="Repeat password"
                                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white">
                                </div>
                            </div>
                        </div>
                    @endguest

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Instructions / Farmer Notes (Optional)</label>
                        <input type="text" name="customer_notes" value="{{ old('customer_notes') }}" placeholder="e.g. Call before delivery, or deliver to farm entrance"
                               class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm space-y-4" x-data="{ paymentMethod: 'cod' }">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center space-x-2">
                        <span>2.</span>
                        <span>Select Payment Method</span>
                    </h2>

                    <div class="space-y-3">
                        <!-- Cash on Delivery -->
                        <label class="flex items-start space-x-3 p-4 rounded-2xl border cursor-pointer transition"
                               :class="paymentMethod === 'cod' ? 'border-sky-600 bg-sky-50/40' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="mt-1 text-sky-700">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">💵 Cash / Pay on Delivery (COD)</span>
                                <span class="text-[11px] text-slate-500">Pay cash or scan QR when your traditional seeds are delivered to your doorstep.</span>
                            </div>
                        </label>

                        <!-- UPI / QR Payment -->
                        <label class="flex items-start space-x-3 p-4 rounded-2xl border cursor-pointer transition"
                               :class="paymentMethod === 'upi' ? 'border-sky-600 bg-sky-50/40' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="payment_method" value="upi" x-model="paymentMethod" class="mt-1 text-sky-700">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">📱 UPI / QR Transfer</span>
                                <span class="text-[11px] text-slate-500">Google Pay, PhonePe, Paytm, BHIM UPI. Payment details provided after placing order.</span>
                            </div>
                        </label>

                        <!-- Direct Bank Transfer -->
                        <label class="flex items-start space-x-3 p-4 rounded-2xl border cursor-pointer transition"
                               :class="paymentMethod === 'bank_transfer' ? 'border-sky-600 bg-sky-50/40' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" class="mt-1 text-sky-700">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🏦 Direct Bank / NEFT / IMPS Transfer</span>
                                <span class="text-[11px] text-slate-500">Transfer directly to initiative bank account. Seeds dispatched upon payment confirmation.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm space-y-5 sticky top-24">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-base font-bold text-slate-900">Your Order ({{ count($items) }})</h2>
                        <a href="{{ route('cart.index') }}" class="text-xs font-bold text-sky-700 hover:underline">Edit Cart</a>
                    </div>

                    <!-- Items mini list -->
                    <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 pr-1 space-y-2">
                        @foreach($items as $item)
                            <div class="pt-2 flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-3">
                                    <span class="w-7 h-7 rounded-lg bg-sky-50 flex items-center justify-center font-bold text-sky-800 flex-shrink-0">
                                        {{ $item['quantity'] }}×
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-800 line-clamp-1">{{ $item['name'] }}</p>
                                        <p class="text-[11px] text-slate-400">₹{{ number_format((float) $item['price'], 2) }} each</p>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-900">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Financial Summary -->
                    <div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-900">₹{{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span>Shipping & Packaging</span>
                            @if($shippingCost == 0)
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">FREE</span>
                            @else
                                <span class="font-semibold text-slate-900">₹{{ number_format($shippingCost, 2) }}</span>
                            @endif
                        </div>

                        <div class="border-t border-slate-100 pt-3 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-slate-900">Grand Total</span>
                            <span class="text-2xl font-black text-forest-800">₹{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-4 rounded-2xl shadow-lg transition text-sm flex items-center justify-center space-x-2">
                        <span>🔒 Place Seed Order</span>
                        <span>&rarr;</span>
                    </button>

                    <p class="text-center text-[11px] text-slate-400">
                        By placing your order, you support traditional seed biodiversity and organic farmer conservationists.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
