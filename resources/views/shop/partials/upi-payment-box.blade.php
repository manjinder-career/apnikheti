@php
    $upiId = $order->getUpiId();
    $payeeName = $order->getUpiPayeeName();
    $customQrUrl = $order->getUpiQrCodeUrl();
    $dynamicQrUrl = $order->getDynamicUpiQrUrl();
    $upiNote = \App\Models\SiteSetting::get('upi_note') ?: 'Please note: Your order will be processed after our team verifies the payment transaction ID.';
    $isPaid = $order->payment_status === 'paid';
    $hasTxnId = !empty($order->transaction_id);
@endphp

<div class="bg-gradient-to-br from-white to-sky-50/50 rounded-3xl border-2 border-sky-200 p-6 md:p-8 shadow-sm space-y-6 text-left" id="upi-payment-section">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-sky-100 gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-800 flex items-center justify-center text-xl font-bold">
                📱
            </div>
            <div>
                <h3 class="text-base font-bold text-forest-800">UPI Payment & Scanner</h3>
                <p class="text-xs text-slate-500">Scan & Pay using Google Pay, PhonePe, Paytm, BHIM or any UPI App</p>
            </div>
        </div>

        <div>
            @if($isPaid)
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span>✓</span>
                    <span>Payment Verified & Paid</span>
                </span>
            @elseif($hasTxnId)
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <span>⏳</span>
                    <span>Verification In Progress</span>
                </span>
            @else
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                    <span>⚠️</span>
                    <span>Payment Awaiting</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Amount & QR Scanner Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
        <!-- Left Column: Scanner Image -->
        <div class="text-center bg-white p-5 rounded-2xl border border-sky-100 shadow-sm flex flex-col items-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Scan QR with Any UPI App</span>

            <div class="relative group">
                @if($customQrUrl)
                    <img src="{{ $customQrUrl }}" alt="Admin UPI Scanner" class="w-52 h-52 object-contain rounded-2xl border border-slate-200 p-2 shadow-inner">
                @else
                    <img src="{{ $dynamicQrUrl }}" alt="UPI QR Code" class="w-52 h-52 rounded-2xl border border-slate-200 p-2 shadow-inner">
                @endif
            </div>

            <p class="text-[11px] text-slate-500 mt-2.5 font-medium">
                Amount to pay: <strong class="text-forest-800 text-sm">₹{{ number_format((float) $order->total_amount, 2) }}</strong>
            </p>
        </div>

        <!-- Right Column: UPI Details & Copy Button -->
        <div class="space-y-4">
            <div class="bg-white p-4 rounded-2xl border border-sky-100 space-y-2 text-xs">
                <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block">Payee Details</span>
                <p class="font-bold text-slate-800 text-sm">{{ $payeeName }}</p>
                <p class="text-slate-500">Order Reference: <strong class="font-mono text-sky-800">{{ $order->order_number }}</strong></p>

                <div class="pt-2">
                    <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block mb-1">Official UPI ID / VPA</span>
                    <div class="flex items-center justify-between bg-sky-50 border border-sky-200 rounded-xl px-3 py-2">
                        <span class="font-mono font-bold text-sky-900 text-sm select-all">{{ $upiId }}</span>
                        <button type="button"
                                onclick="navigator.clipboard.writeText('{{ $upiId }}'); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy', 2000)"
                                class="text-xs bg-sky-700 hover:bg-sky-800 text-white px-3 py-1 rounded-lg font-bold transition shadow-sm">
                            Copy
                        </button>
                    </div>
                </div>
            </div>

            <!-- MANDATORY USER NOTE -->
            <div class="bg-amber-50 border-l-4 border-amber-500 p-3.5 rounded-r-2xl text-xs text-amber-900 space-y-1">
                <div class="flex items-center space-x-1.5 font-bold">
                    <span>⚠️ Important Notice / ਜ਼ਰੂਰੀ ਸੂਚਨਾ:</span>
                </div>
                <p class="font-semibold leading-relaxed">{{ $upiNote }}</p>
                <p class="text-[11px] text-amber-800 font-gurmukhi">
                    ਕਿਰਪਾ ਕਰਕੇ ਧਿਆਨ ਦਿਓ: ਭੁਗਤਾਨ ਦੀ ਪੁਸ਼ਟੀ ਹੋਣ ਉਪਰੰਤ ਹੀ ਸਾਡੀ ਟੀਮ ਦੁਆਰਾ ਤੁਹਾਡਾ ਆਰਡਰ ਪੈਕ ਤੇ ਰਵਾਨਾ ਕੀਤਾ ਜਾਵੇਗਾ।
                </p>
            </div>
        </div>
    </div>

    <!-- Bottom: Submit Transaction ID Form / Status -->
    <div class="bg-white rounded-2xl p-5 border border-sky-100 space-y-3">
        @if($isPaid)
            <div class="flex items-center space-x-3 text-xs text-emerald-800">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                    ✓
                </div>
                <div>
                    <span class="font-bold block">Payment Successfully Verified!</span>
                    <p class="text-emerald-700 text-[11px]">
                        @if($order->transaction_id)
                            Transaction / UTR ID: <code class="font-mono font-bold bg-emerald-50 px-1 py-0.5 rounded border border-emerald-200">{{ $order->transaction_id }}</code> •
                        @endif
                        Verified on {{ $order->payment_verified_at?->format('M d, Y • h:i A') ?? 'Verified' }}. Your order is being processed.
                    </p>
                </div>
            </div>
        @else
            <div x-data="{ editing: {{ ($hasTxnId && ! $errors->has('transaction_id')) ? 'false' : 'true' }} }">
                @if($hasTxnId)
                    <div x-show="!editing" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-start space-x-2.5">
                            <span class="text-lg">⏳</span>
                            <div>
                                <span class="font-bold text-slate-800">Submitted Transaction ID / UTR:</span>
                                <span class="font-mono font-bold text-sky-800 text-sm ml-1 bg-sky-50 px-2 py-0.5 rounded border border-sky-100">{{ $order->transaction_id }}</span>
                                <p class="text-slate-500 text-[11px] mt-0.5">Payment is pending verification by our seed coordinator team. Your order will process once approved.</p>
                            </div>
                        </div>
                        <button type="button" @click="editing = true" class="text-sky-700 hover:text-sky-900 font-bold underline text-xs whitespace-nowrap self-start sm:self-center">
                            Change / Correct ID
                        </button>
                    </div>
                @endif

                <form x-show="editing" action="{{ route('orders.payment.submit', $order->order_number) }}" method="POST" class="space-y-3 pt-2">
                    @csrf
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Enter UPI Transaction ID / UTR Number <span class="text-red-500">*</span>
                            </label>
                            @if($hasTxnId)
                                <button type="button" @click="editing = false" class="text-slate-400 hover:text-slate-600 text-xs">Cancel</button>
                            @endif
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" name="transaction_id" required
                                   value="{{ old('transaction_id', $order->transaction_id) }}"
                                   placeholder="e.g. 12-digit UTR: 429182749182 or Ref: UPI/2026/..."
                                   class="flex-1 text-xs px-3.5 py-3 rounded-xl border {{ $errors->has('transaction_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} focus:outline-none focus:border-sky-600 font-mono">
                            <button type="submit" class="bg-forest-700 hover:bg-forest-800 text-white font-bold px-5 py-3 rounded-xl text-xs shadow transition whitespace-nowrap">
                                <span>Submit Transaction ID &rarr;</span>
                            </button>
                        </div>

                        @error('transaction_id')
                            <div class="mt-2 bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-xl text-xs flex items-center space-x-1.5 font-semibold">
                                <span>❌</span>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <p class="text-[11px] text-slate-400 mt-1">
                            Find the 12-digit UPI Transaction / UTR ID in your Google Pay, PhonePe, Paytm, or BHIM transaction details. Each transaction ID can only be used once.
                        </p>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
