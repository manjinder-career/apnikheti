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

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form"
          x-data="{
              subtotal: {{ (float) $subtotal }},
              shippingCost: {{ (float) $shippingCost }},
              total: {{ (float) $total }},
              state: '{{ addslashes(old('shipping_state', $user->state ?? 'Punjab')) }}',
              weightFormatted: '{{ $shippingBreakdown['formatted_weight'] }}',
              isPunjab: {{ $shippingBreakdown['is_punjab'] ? 'true' : 'false' }},
              isFree: {{ $shippingBreakdown['is_free'] ? 'true' : 'false' }},
              regionLabel: '{{ $shippingBreakdown['region_label'] }}',
              loadingQuote: false,
              async updateShipping() {
                  if (!this.state || this.state.trim().length === 0) return;
                  this.loadingQuote = true;
                  try {
                      const res = await fetch('{{ route('checkout.shipping-quote') }}?state=' + encodeURIComponent(this.state));
                      if (res.ok) {
                          const data = await res.json();
                          this.shippingCost = data.shippingCost;
                          this.total = data.total;
                          this.isFree = data.isFree;
                          this.isPunjab = data.isPunjab;
                          this.regionLabel = data.regionLabel;
                          this.weightFormatted = data.formattedWeight;
                      }
                  } catch(e) {
                      console.error('Shipping quote error', e);
                  } finally {
                      this.loadingQuote = false;
                  }
              }
          }">
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
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">State <span class="text-red-500">*</span></label>
                                <span class="text-[10px] text-slate-400" x-show="loadingQuote">Calculating rate...</span>
                            </div>
                            <input type="text" name="shipping_state" required list="indian-states"
                                   x-model="state" @input.debounce.300ms="updateShipping()" @change="updateShipping()"
                                   placeholder="e.g. Punjab"
                                   class="w-full text-xs px-3.5 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 font-medium">
                            <datalist id="indian-states">
                                <option value="Punjab">
                                <option value="Haryana">
                                <option value="Delhi">
                                <option value="Himachal Pradesh">
                                <option value="Rajasthan">
                                <option value="Uttar Pradesh">
                                <option value="Jammu and Kashmir">
                                <option value="Uttarakhand">
                                <option value="Chandigarh">
                                <option value="Maharashtra">
                                <option value="Gujarat">
                                <option value="Madhya Pradesh">
                                <option value="Bihar">
                                <option value="West Bengal">
                                <option value="Karnataka">
                                <option value="Tamil Nadu">
                                <option value="Telangana">
                                <option value="Kerala">
                            </datalist>
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
                @php
                    $isBoth = ($enableSimpleUpi ?? true) && ($enableRazorpay ?? true);
                    $selectedMethod = old('payment_method', ($defaultMethod ?? ($enableRazorpay ? 'razorpay' : 'upi')));
                @endphp

                <div class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm space-y-4"
                     x-data="{ selectedMethod: '{{ $selectedMethod }}' }">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                            <span>2.</span>
                            <span>Payment Method</span>
                        </h2>
                        <span class="text-[11px] font-bold bg-sky-100 text-sky-800 px-2.5 py-0.5 rounded-full">
                            @if($isBoth)
                                Choose Payment Option
                            @elseif($enableRazorpay ?? false)
                                Razorpay Gateway
                            @else
                                Simple UPI Payment
                            @endif
                        </span>
                    </div>

                    @error('payment_method')
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-semibold">
                            ⚠️ {{ $message }}
                        </div>
                    @enderror

                    <div class="space-y-3">
                        @if($enableRazorpay ?? true)
                            <!-- Razorpay Option Card -->
                            <label class="block cursor-pointer transition"
                                   @click="selectedMethod = 'razorpay'">
                                <div class="rounded-2xl p-4 md:p-5 border-2 transition"
                                     :class="selectedMethod === 'razorpay' ? 'border-forest-700 bg-gradient-to-r from-emerald-50/70 via-white to-emerald-50/30 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <input type="radio" name="payment_method" value="razorpay"
                                                   x-model="selectedMethod"
                                                   class="mt-1 text-forest-700 focus:ring-forest-600">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-base">⚡</span>
                                                    <span class="text-xs md:text-sm font-bold text-slate-900">Razorpay Payment Gateway</span>
                                                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-200">
                                                        Instant Confirmation
                                                    </span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    Pay securely using UPI Apps (Google Pay, PhonePe, Paytm), Cards, Net Banking & Wallets.
                                                </p>
                                            </div>
                                        </div>
                                        <span class="hidden sm:inline-flex text-xs font-bold px-2 py-0.5 rounded-md"
                                              :class="selectedMethod === 'razorpay' ? 'bg-forest-700 text-white' : 'text-slate-400'">
                                            <span x-text="selectedMethod === 'razorpay' ? 'Selected' : 'Select'"></span>
                                        </span>
                                    </div>

                                    <div x-show="selectedMethod === 'razorpay'" class="mt-3.5 pt-3 border-t border-emerald-100 text-xs text-slate-600 space-y-2">
                                        <div class="flex flex-wrap gap-1.5 text-[11px]">
                                            <span class="bg-white border border-emerald-200 px-2 py-0.5 rounded-md text-emerald-900 font-semibold">✓ Google Pay / PhonePe / Paytm</span>
                                            <span class="bg-white border border-emerald-200 px-2 py-0.5 rounded-md text-emerald-900 font-semibold">✓ All Debit & Credit Cards</span>
                                            <span class="bg-white border border-emerald-200 px-2 py-0.5 rounded-md text-emerald-900 font-semibold">✓ 50+ Netbanking Banks</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500">
                                            A secure Razorpay checkout screen will open to complete payment instantly.
                                        </p>
                                    </div>
                                </div>
                            </label>
                        @endif

                        @if($enableSimpleUpi ?? true)
                            <!-- Simple UPI QR Option Card -->
                            <label class="block cursor-pointer transition"
                                   @click="selectedMethod = 'upi'">
                                <div class="rounded-2xl p-4 md:p-5 border-2 transition"
                                     :class="selectedMethod === 'upi' ? 'border-sky-600 bg-gradient-to-r from-sky-50/70 via-white to-sky-50/30 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <input type="radio" name="payment_method" value="upi"
                                                   x-model="selectedMethod"
                                                   class="mt-1 text-sky-700 focus:ring-sky-600">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-base">📱</span>
                                                    <span class="text-xs md:text-sm font-bold text-slate-900">Simple UPI / QR Code Transfer</span>
                                                    <span class="text-[10px] font-bold bg-sky-100 text-sky-800 px-2 py-0.5 rounded-full border border-sky-200">
                                                        Manual Verification
                                                    </span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    Scan our UPI QR code directly or pay to our UPI ID, then submit your 12-digit UTR ID.
                                                </p>
                                            </div>
                                        </div>
                                        <span class="hidden sm:inline-flex text-xs font-bold px-2 py-0.5 rounded-md"
                                              :class="selectedMethod === 'upi' ? 'bg-sky-700 text-white' : 'text-slate-400'">
                                            <span x-text="selectedMethod === 'upi' ? 'Selected' : 'Select'"></span>
                                        </span>
                                    </div>

                                    <div x-show="selectedMethod === 'upi'" class="mt-3.5 pt-3 border-t border-sky-100 text-xs text-slate-600 space-y-2">
                                        <p class="text-xs text-slate-600 bg-white/90 p-3 rounded-xl border border-sky-100 leading-relaxed">
                                            Official UPI ID and QR code scanner will appear immediately after placing order. You can submit your 12-digit transaction ID anytime.
                                        </p>
                                        <div class="flex items-start space-x-2 text-[11px] text-amber-900 bg-amber-50/90 p-3 rounded-xl border border-amber-200">
                                            <span class="text-base leading-none">⚠️</span>
                                            <span class="leading-relaxed">
                                                <strong>Notice:</strong> Your order will be packed and dispatched after our seed conservation team manually verifies your transaction ID.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endif
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
                    <div class="border-t border-slate-100 pt-4 space-y-2.5 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-900">₹{{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-[11px] text-slate-500">
                            <span>Total Package Weight</span>
                            <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                                📦 <span x-text="weightFormatted">{{ $shippingBreakdown['formatted_weight'] }}</span>
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <div>
                                <span>Shipping & Packaging</span>
                                <span class="block text-[10px] text-slate-400" x-text="regionLabel">
                                    {{ $shippingBreakdown['region_label'] }}
                                </span>
                            </div>
                            <div>
                                <template x-if="isFree">
                                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">FREE</span>
                                </template>
                                <template x-if="!isFree">
                                    <span class="font-semibold text-slate-900" x-text="'₹' + Number(shippingCost).toFixed(2)">
                                        ₹{{ number_format($shippingCost, 2) }}
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-3 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-slate-900">Grand Total</span>
                            <span class="text-2xl font-black text-forest-800" x-text="'₹' + Number(total).toFixed(2)">
                                ₹{{ number_format($total, 2) }}
                            </span>
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
