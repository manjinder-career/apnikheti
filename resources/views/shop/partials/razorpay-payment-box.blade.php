@php
    $razorpayService = app(\App\Services\RazorpayService::class);
    $keyId = $razorpayService->getKeyId();
    $merchantName = $razorpayService->getMerchantName();
    $isPaid = $order->payment_status === 'paid';
    $amountInPaise = (int) round(((float) $order->total_amount) * 100);
    $rzpOrderId = $order->razorpay_order_id ?: ('order_' . \Illuminate\Support\Str::random(14));
@endphp

<div class="bg-gradient-to-br from-white to-emerald-50/40 rounded-3xl border-2 border-emerald-200 p-6 md:p-8 shadow-sm space-y-6 text-left" id="razorpay-payment-section">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-emerald-100 gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold">
                ⚡
            </div>
            <div>
                <h3 class="text-base font-bold text-forest-800">Razorpay Online Payment Gateway</h3>
                <p class="text-xs text-slate-500">Pay securely via UPI (GPay, PhonePe, Paytm), Credit/Debit Card, Net Banking or Wallets</p>
            </div>
        </div>

        <div>
            @if($isPaid)
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span>✓</span>
                    <span>Payment Verified & Paid</span>
                </span>
            @else
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200">
                    <span>⚡</span>
                    <span>Ready for Payment</span>
                </span>
            @endif
        </div>
    </div>

    @if($isPaid)
        <!-- Paid State -->
        <div class="bg-white rounded-2xl p-5 border border-emerald-100 space-y-3">
            <div class="flex items-start space-x-3.5 text-xs text-emerald-800">
                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                    ✓
                </div>
                <div class="space-y-1">
                    <span class="font-bold text-sm block text-emerald-900">Payment Successfully Received & Confirmed!</span>
                    <p class="text-emerald-700 text-xs">
                        Transaction / Payment ID:
                        <code class="font-mono font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-slate-800">
                            {{ $order->razorpay_payment_id ?: $order->transaction_id }}
                        </code>
                    </p>
                    @if($order->razorpay_order_id)
                        <p class="text-slate-500 text-[11px]">
                            Razorpay Order ID: <span class="font-mono">{{ $order->razorpay_order_id }}</span>
                        </p>
                    @endif
                    <p class="text-slate-500 text-[11px] pt-1">
                        Verified on {{ $order->payment_verified_at?->format('M d, Y • h:i A') ?? 'Verified' }}.
                        Our seed team has scheduled this order for packaging and dispatch.
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Pending State / Payment Trigger -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <!-- Left: Order summary -->
            <div class="bg-white p-5 rounded-2xl border border-emerald-100 space-y-3 text-xs">
                <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block">Payment Summary</span>
                <div class="flex justify-between items-baseline border-b pb-2">
                    <span class="text-slate-600">Order Number</span>
                    <strong class="font-mono text-slate-900">{{ $order->order_number }}</strong>
                </div>
                <div class="flex justify-between items-baseline border-b pb-2">
                    <span class="text-slate-600">Total Payable Amount</span>
                    <strong class="text-xl font-black text-forest-800">₹{{ number_format((float) $order->total_amount, 2) }}</strong>
                </div>
                <div class="pt-1">
                    <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block mb-1.5">Accepted Methods</span>
                    <div class="flex flex-wrap gap-1.5 text-[11px]">
                        <span class="bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-lg text-slate-700 font-semibold">📱 UPI Apps</span>
                        <span class="bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-lg text-slate-700 font-semibold">💳 Credit / Debit Card</span>
                        <span class="bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-lg text-slate-700 font-semibold">🏦 Net Banking</span>
                        <span class="bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-lg text-slate-700 font-semibold">👛 Wallets</span>
                    </div>
                </div>
            </div>

            <!-- Right: Action Button & Modal Launcher -->
            <div class="space-y-3">
                <div class="bg-white p-5 rounded-2xl border border-emerald-100 text-center space-y-4">
                    <div>
                        <span class="text-xs text-slate-500 block">Click below to open the secure Razorpay payment modal:</span>
                    </div>

                    <button type="button" id="rzp-pay-button"
                            class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3.5 px-6 rounded-xl shadow-md transition flex items-center justify-center space-x-2 text-sm cursor-pointer">
                        <span>🔒 Pay ₹{{ number_format((float) $order->total_amount, 2) }} via Razorpay</span>
                        <span>&rarr;</span>
                    </button>

                    <div class="flex items-center justify-center space-x-2 text-[11px] text-slate-400">
                        <span>🛡️ 256-bit SSL Encrypted</span>
                        <span>•</span>
                        <span>Instant Order Confirmation</span>
                    </div>
                </div>

                @if(empty($keyId))
                    <!-- Notice & Simulation button if keys not yet configured in admin -->
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900 space-y-2">
                        <div class="flex items-center space-x-1.5 font-bold">
                            <span>ℹ️ Razorpay Test Notice:</span>
                        </div>
                        <p class="text-[11px] leading-relaxed">
                            Admin has not saved a Razorpay Key ID yet in Admin Settings.
                            You can add keys in <a href="{{ route('admin.settings.index') }}" class="underline font-bold text-amber-900" target="_blank">Admin Settings &rarr; Payment Methods</a>.
                        </p>
                        <form action="{{ route('orders.razorpay.verify', $order->order_number) }}" method="POST" class="pt-1">
                            @csrf
                            <input type="hidden" name="razorpay_order_id" value="{{ $rzpOrderId }}">
                            <input type="hidden" name="razorpay_payment_id" value="pay_sim_{{ \Illuminate\Support\Str::random(12) }}">
                            <input type="hidden" name="razorpay_signature" value="simulated_signature">
                            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-3 rounded-lg text-xs transition shadow-sm">
                                🧪 Simulate Successful Razorpay Payment (Demo Mode)
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Hidden form for Razorpay callback submission -->
        <form id="razorpay-verification-form" action="{{ route('orders.razorpay.verify', $order->order_number) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="razorpay_order_id" id="rzp_form_order_id">
            <input type="hidden" name="razorpay_payment_id" id="rzp_form_payment_id">
            <input type="hidden" name="razorpay_signature" id="rzp_form_signature">
        </form>

        <!-- Razorpay Checkout JavaScript SDK -->
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var payButton = document.getElementById('rzp-pay-button');
                if (!payButton) return;

                payButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    var key = "{{ $keyId }}";
                    if (!key) {
                        alert('Razorpay Key ID is not configured in Admin Settings. You can click the Demo Simulation button below, or add your Razorpay API keys in Admin Settings.');
                        return;
                    }

                    var options = {
                        "key": key,
                        "amount": "{{ $amountInPaise }}",
                        "currency": "INR",
                        "name": "{{ addslashes($merchantName) }}",
                        "description": "Order #{{ $order->order_number }} - Traditional Seeds",
                        "order_id": "{{ $rzpOrderId }}",
                        "prefill": {
                            "name": "{{ addslashes($order->customer_name) }}",
                            "email": "{{ addslashes($order->customer_email) }}",
                            "contact": "{{ addslashes($order->customer_phone) }}"
                        },
                        "theme": {
                            "color": "#15803d"
                        },
                        "handler": function (response) {
                            // Populate verification form and submit
                            document.getElementById('rzp_form_order_id').value = response.razorpay_order_id;
                            document.getElementById('rzp_form_payment_id').value = response.razorpay_payment_id;
                            document.getElementById('rzp_form_signature').value = response.razorpay_signature;

                            payButton.disabled = true;
                            payButton.innerText = 'Verifying Payment...';

                            document.getElementById('razorpay-verification-form').submit();
                        },
                        "modal": {
                            "ondismiss": function() {
                                console.log('Razorpay modal closed');
                            }
                        }
                    };

                    try {
                        var rzp = new Razorpay(options);
                        rzp.on('payment.failed', function (response) {
                            alert('Payment could not be completed: ' + (response.error.description || 'Unknown error.'));
                        });
                        rzp.open();
                    } catch (err) {
                        console.error('Razorpay initialization error:', err);
                        alert('Could not initialize Razorpay checkout. ' + err.message);
                    }
                });
            });
        </script>
    @endif
</div>
