@extends('layouts.admin')

@section('title', 'Site Settings')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Site Settings Management</h1>
        <p class="text-xs text-gray-500">Edit founder biography, slogans, verified seed statistics, and contact information without touching source code.</p>
    </div>

    @if($errors->has('payment_methods'))
        <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-xl text-xs font-semibold shadow-sm">
            ❌ {{ $errors->first('payment_methods') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border shadow-sm space-y-6">
        @csrf

        <!-- General Slogans -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">1. Initiative Slogans & Branding</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Punjabi Slogan (ਪੰਜਾਬੀ ਸਲੋਗਨ)</label>
                    <input type="text" name="slogan_pa" value="{{ $settings['slogan_pa'] ?? 'ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ' }}" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">English Slogan</label>
                    <input type="text" name="slogan_en" value="{{ $settings['slogan_en'] ?? 'Our Farming. Our Seeds. Our Future.' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
            </div>
        </div>

        <!-- Founder Profile (Amrit Singh Chahal) -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">2. Founder Biography (Amrit Singh Chahal)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Founder / Practitioner Name</label>
                    <input type="text" name="founder_name" value="{{ $settings['founder_name'] ?? 'Amrit Singh Chahal' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Title / Role</label>
                    <input type="text" name="founder_title" value="{{ $settings['founder_title'] ?? 'Seed Conservationist & Natural Farming Advocate' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Founder Biography & Background Summary</label>
                <textarea name="founder_bio" rows="4" class="w-full text-xs p-2.5 border rounded-lg">{{ $settings['founder_bio'] ?? '' }}</textarea>
            </div>
        </div>

        <!-- Seed Collection Statistics Counter -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">3. Seed Collection Statistics</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Verified Seed Variety Count (e.g. 500+)</label>
                    <input type="text" name="verified_seed_count" value="{{ $settings['verified_seed_count'] ?? '500+' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Display Verified Statistic on Homepage</label>
                    <select name="display_verified_stat" class="w-full text-xs p-2.5 border rounded-lg">
                        <option value="1" {{ ($settings['display_verified_stat'] ?? '1') == '1' ? 'selected' : '' }}>Yes — Display Stat</option>
                        <option value="0" {{ ($settings['display_verified_stat'] ?? '1') == '0' ? 'selected' : '' }}>No — Hide Stat</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b pb-1">4. Editable Contact Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'info@apnikheti.org' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+91 98765 43210' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Location Address</label>
                    <input type="text" name="contact_address" value="{{ $settings['contact_address'] ?? 'Punjab, India' }}" class="w-full text-xs p-2.5 border rounded-lg">
                </div>
            </div>
        </div>

        <!-- 5. Payment Methods & Gateway Settings (Simple UPI & Razorpay) -->
        <input type="hidden" name="payment_settings_submitted" value="1">
        <div class="space-y-6 border-t pt-5" id="payment-settings">
            <div class="flex items-center justify-between border-b pb-2">
                <div>
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">5. Payment Methods & Gateway Settings</h2>
                    <p class="text-[11px] text-gray-500">Configure Razorpay payment gateway and Simple UPI Pay. Enable either or both payment options for customers.</p>
                </div>
                <span class="text-[11px] text-forest-700 font-semibold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">Payment Controls</span>
            </div>

            <!-- Master Payment Method Toggles -->
            <div class="bg-gray-50 border rounded-2xl p-5 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">A. Active Payment Methods (Enable / Disable)</h3>
                <p class="text-xs text-gray-500">Select which payment methods will appear on the customer checkout page. At least one method must remain active.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Toggle Simple UPI -->
                    <label class="flex items-start space-x-3 bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-pointer hover:border-sky-400 transition">
                        <input type="checkbox" name="enable_simple_upi" value="1"
                               {{ filter_var($settings['enable_simple_upi'] ?? '1', FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                               class="mt-1 rounded text-sky-600 focus:ring-sky-500">
                        <div>
                            <span class="text-xs font-bold text-gray-800 flex items-center space-x-1.5">
                                <span>📱</span>
                                <span>Enable Simple UPI Pay</span>
                            </span>
                            <span class="text-[11px] text-gray-500 block mt-0.5 leading-relaxed">
                                Direct UPI ID & QR Code scanner with manual customer UTR submission & verification.
                            </span>
                        </div>
                    </label>

                    <!-- Toggle Razorpay -->
                    <label class="flex items-start space-x-3 bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-pointer hover:border-forest-600 transition">
                        <input type="checkbox" name="enable_razorpay" value="1"
                               {{ filter_var($settings['enable_razorpay'] ?? '1', FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                               class="mt-1 rounded text-forest-700 focus:ring-forest-600">
                        <div>
                            <span class="text-xs font-bold text-gray-800 flex items-center space-x-1.5">
                                <span>⚡</span>
                                <span>Enable Razorpay Gateway</span>
                            </span>
                            <span class="text-[11px] text-gray-500 block mt-0.5 leading-relaxed">
                                Automated instant payments via Cards, UPI apps, Net Banking & Wallets.
                            </span>
                        </div>
                    </label>
                </div>

                <div class="pt-2 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <label class="font-semibold text-gray-700 block">Default Checkout Option</label>
                        <span class="text-[11px] text-gray-500">Pre-selected option on the checkout page when both methods are enabled.</span>
                    </div>
                    <select name="default_payment_method" class="text-xs p-2 rounded-lg border border-gray-300 bg-white">
                        <option value="razorpay" {{ ($settings['default_payment_method'] ?? 'razorpay') === 'razorpay' ? 'selected' : '' }}>Razorpay Gateway (Instant)</option>
                        <option value="upi" {{ ($settings['default_payment_method'] ?? 'razorpay') === 'upi' ? 'selected' : '' }}>Simple UPI Pay (Manual QR)</option>
                    </select>
                </div>
            </div>

            <!-- Razorpay Gateway Settings Sub-Panel -->
            <div class="bg-white border rounded-2xl p-5 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">⚡</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-forest-800">B. Razorpay Gateway API Configuration</h3>
                    </div>
                    @if(!empty($settings['razorpay_key_id']))
                        <span class="text-[10px] font-bold bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded border border-emerald-200">
                            ✓ Key Configured
                        </span>
                    @else
                        <span class="text-[10px] font-bold bg-amber-50 text-amber-800 px-2 py-0.5 rounded border border-amber-200">
                            API Key Pending
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Razorpay Environment Mode</label>
                        <select name="razorpay_mode" class="w-full text-xs p-2.5 border rounded-lg bg-gray-50">
                            <option value="test" {{ ($settings['razorpay_mode'] ?? 'test') === 'test' ? 'selected' : '' }}>Test / Sandbox Mode (rzp_test_...)</option>
                            <option value="live" {{ ($settings['razorpay_mode'] ?? 'test') === 'live' ? 'selected' : '' }}>Live Production Mode (rzp_live_...)</option>
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1">Use Test mode for sandbox payments, switch to Live when ready to collect actual funds.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Razorpay Merchant Brand Name</label>
                        <input type="text" name="razorpay_merchant_name"
                               value="{{ $settings['razorpay_merchant_name'] ?? 'Apni Kheti Apne Beej' }}"
                               placeholder="e.g. Apni Kheti Apne Beej"
                               class="w-full text-xs p-2.5 border rounded-lg">
                        <p class="text-[10px] text-gray-500 mt-1">Displayed in the header of the Razorpay Checkout popup.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Razorpay Key ID</label>
                        <input type="text" name="razorpay_key_id"
                               value="{{ $settings['razorpay_key_id'] ?? '' }}"
                               placeholder="e.g. rzp_test_1DP5mmOlF5G5ag or rzp_live_..."
                               class="w-full text-xs p-2.5 border rounded-lg font-mono">
                        <p class="text-[10px] text-gray-500 mt-1">Found in your Razorpay Dashboard &rarr; Settings &rarr; API Keys.</p>
                    </div>

                    <div x-data="{ showSecret: false }">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-semibold text-gray-700">Razorpay Key Secret</label>
                            <button type="button" @click="showSecret = !showSecret" class="text-[10px] text-forest-700 font-bold hover:underline">
                                <span x-text="showSecret ? 'Hide' : 'Show'"></span>
                            </button>
                        </div>
                        <input :type="showSecret ? 'text' : 'password'"
                               name="razorpay_key_secret"
                               value="{{ $settings['razorpay_key_secret'] ?? '' }}"
                               placeholder="Razorpay API Key Secret"
                               class="w-full text-xs p-2.5 border rounded-lg font-mono">
                        <p class="text-[10px] text-gray-500 mt-1">Used for HMAC SHA-256 signature verification to prevent payment tampering.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Razorpay Webhook Secret (Optional)</label>
                    <input type="text" name="razorpay_webhook_secret"
                           value="{{ $settings['razorpay_webhook_secret'] ?? '' }}"
                           placeholder="Webhook secret configured in Razorpay Dashboard"
                           class="w-full text-xs p-2.5 border rounded-lg font-mono">
                    <p class="text-[10px] text-gray-500 mt-1">Used to verify background asynchronous payment events from Razorpay.</p>
                </div>

                <!-- Webhook Callback URL Banner -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 text-xs text-gray-600 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="font-bold text-gray-800 block text-[11px] uppercase tracking-wider">Razorpay Webhook Endpoint URL:</span>
                        <code class="font-mono text-forest-800 text-[11px] font-bold select-all">{{ url('/webhooks/razorpay') }}</code>
                        <span class="block text-[10px] text-gray-400 mt-0.5">Subscribe to: <code class="bg-white px-1 py-0.5 rounded border">payment.captured</code> and <code class="bg-white px-1 py-0.5 rounded border">order.paid</code></span>
                    </div>
                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ url('/webhooks/razorpay') }}'); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy URL', 2000)"
                            class="bg-white border text-gray-700 hover:text-gray-900 text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-sm whitespace-nowrap self-start sm:self-center">
                        Copy URL
                    </button>
                </div>
            </div>

            <!-- Simple UPI & QR Scanner Settings Sub-Panel -->
            <div class="bg-white border rounded-2xl p-5 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">📱</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-sky-900">C. Simple UPI & Direct QR Scanner Settings</h3>
                    </div>
                    <span class="text-[11px] text-sky-700 font-semibold bg-sky-50 px-2.5 py-0.5 rounded-full border border-sky-200">
                        Manual Verification
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Admin UPI ID / VPA *</label>
                        <input type="text" name="upi_id" value="{{ $settings['upi_id'] ?? 'apnikheti@sbi' }}" placeholder="e.g. apnikheti@sbi or 9876543210@paytm" class="w-full text-xs p-2.5 border rounded-lg font-mono">
                        <p class="text-[10px] text-gray-500 mt-1">Customers can send direct payments to this UPI VPA address.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">UPI Payee / Account Name</label>
                        <input type="text" name="upi_payee_name" value="{{ $settings['upi_payee_name'] ?? 'Apni Kheti Apne Beej' }}" placeholder="e.g. Apni Kheti Seed Bank" class="w-full text-xs p-2.5 border rounded-lg">
                        <p class="text-[10px] text-gray-500 mt-1">Beneficiary name that displays on the customer's UPI app.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Upload UPI QR Scanner Image</label>
                        <input type="file" name="upi_qr_code" accept="image/*" class="w-full text-xs p-2 border rounded-lg bg-gray-50">
                        <p class="text-[10px] text-gray-500 mt-1">Upload your official QR code standee or scanner photo (PNG, JPG, SVG). An auto-generated dynamic UPI QR code is also provided.</p>
                    </div>

                    @if(!empty($settings['upi_qr_code']) && file_exists(public_path('storage/' . $settings['upi_qr_code'])))
                        <div class="bg-gray-50 border p-3 rounded-lg flex items-center space-x-4">
                            <img src="{{ asset('storage/' . $settings['upi_qr_code']) }}" alt="UPI QR" class="w-20 h-20 object-contain rounded border bg-white p-1">
                            <div class="text-xs">
                                <span class="font-bold text-gray-800 block">Current Scanner Image</span>
                                <label class="inline-flex items-center space-x-1.5 mt-2 cursor-pointer text-rose-600 text-[11px] font-semibold">
                                    <input type="checkbox" name="remove_upi_qr_code" value="1" class="rounded text-rose-600">
                                    <span>Remove uploaded scanner</span>
                                </label>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Order Processing Note (Shown to Customer) *</label>
                    <textarea name="upi_note" rows="2" class="w-full text-xs p-2.5 border rounded-lg">{{ $settings['upi_note'] ?? 'Please note: Your order will be processed after our team verifies the payment transaction ID.' }}</textarea>
                    <p class="text-[10px] text-gray-500 mt-1">This important note informs customers that their order will only be dispatched upon manual payment verification.</p>
                </div>
            </div>
        </div>

        <!-- 6. Weight-Based Shipping & Delivery Rates (Punjab vs Outside Punjab) -->
        <div class="space-y-6 border-t pt-5" id="shipping-settings">
            <div class="flex items-center justify-between border-b pb-2">
                <div>
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">6. Shipping & Delivery Rates (Weight-Based)</h2>
                    <p class="text-[11px] text-gray-500">Configure base rates (up to 1 Kg), additional per-Kg / per-gram rates, and free delivery thresholds for Punjab and Outside Punjab.</p>
                </div>
                <span class="text-[11px] text-sky-800 font-semibold bg-sky-50 px-2.5 py-0.5 rounded-full border border-sky-200">Weight & Region Rates</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Punjab Rates Card -->
                <div class="bg-gradient-to-br from-white to-emerald-50/30 border-2 border-emerald-200 rounded-2xl p-5 space-y-4 shadow-sm">
                    <div class="flex items-center justify-between border-b border-emerald-100 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">📍</span>
                            <div>
                                <h3 class="text-xs font-bold text-forest-800 uppercase tracking-wider">Punjab (Local Delivery)</h3>
                                <p class="text-[10px] text-slate-500">Intra-state courier deliveries</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-200">
                            Local Rate
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Base Shipping Cost (Up to 1 Kg / 1000g) *</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l-lg text-xs font-bold text-gray-600">₹</span>
                                <input type="number" step="0.01" min="0" name="shipping_punjab_base_cost"
                                       value="{{ $settings['shipping_punjab_base_cost'] ?? '50.00' }}"
                                       class="w-full text-xs p-2.5 border rounded-r-lg font-mono">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Charged for all orders weighing between 1g and 1000g.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Additional Cost per Extra Kg (After 1st Kg) *</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l-lg text-xs font-bold text-gray-600">₹</span>
                                <input type="number" step="0.01" min="0" name="shipping_punjab_per_kg"
                                       value="{{ $settings['shipping_punjab_per_kg'] ?? '30.00' }}"
                                       class="w-full text-xs p-2.5 border rounded-r-lg font-mono">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">₹30/Kg equals ₹0.03 per gram. Rounded to courier slabs.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Free Shipping Order Minimum (Subtotal Threshold) *</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l-lg text-xs font-bold text-gray-600">₹</span>
                                <input type="number" step="0.01" min="0" name="shipping_punjab_free_threshold"
                                       value="{{ $settings['shipping_punjab_free_threshold'] ?? '500.00' }}"
                                       class="w-full text-xs p-2.5 border rounded-r-lg font-mono">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Orders above this amount get 100% Free Delivery in Punjab (Enter 0 to disable).</p>
                        </div>
                    </div>
                </div>

                <!-- Outside Punjab Rates Card -->
                <div class="bg-gradient-to-br from-white to-sky-50/30 border-2 border-sky-200 rounded-2xl p-5 space-y-4 shadow-sm">
                    <div class="flex items-center justify-between border-b border-sky-100 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">🚚</span>
                            <div>
                                <h3 class="text-xs font-bold text-sky-900 uppercase tracking-wider">Outside Punjab (Rest of India)</h3>
                                <p class="text-[10px] text-slate-500">Interstate courier deliveries across India</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold bg-sky-100 text-sky-800 px-2 py-0.5 rounded-full border border-sky-200">
                            National Rate
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Base Shipping Cost (Up to 1 Kg / 1000g) *</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l-lg text-xs font-bold text-gray-600">₹</span>
                                <input type="number" step="0.01" min="0" name="shipping_outside_base_cost"
                                       value="{{ $settings['shipping_outside_base_cost'] ?? '80.00' }}"
                                       class="w-full text-xs p-2.5 border rounded-r-lg font-mono">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Charged for all interstate orders weighing between 1g and 1000g.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Additional Cost per Extra Kg (After 1st Kg) *</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l-lg text-xs font-bold text-gray-600">₹</span>
                                <input type="number" step="0.01" min="0" name="shipping_outside_per_kg"
                                       value="{{ $settings['shipping_outside_per_kg'] ?? '50.00' }}"
                                       class="w-full text-xs p-2.5 border rounded-r-lg font-mono">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">₹50/Kg equals ₹0.05 per gram for interstate shipments.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Free Shipping Order Minimum (Subtotal Threshold) *</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l-lg text-xs font-bold text-gray-600">₹</span>
                                <input type="number" step="0.01" min="0" name="shipping_outside_free_threshold"
                                       value="{{ $settings['shipping_outside_free_threshold'] ?? '800.00' }}"
                                       class="w-full text-xs p-2.5 border rounded-r-lg font-mono">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Interstate orders above this amount get Free Delivery (Enter 0 to disable).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calculation Example Helper Box -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-xs text-slate-600 space-y-1.5">
                <div class="font-bold text-slate-800 flex items-center space-x-1.5">
                    <span>💡 How Courier Calculation Works:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-500">
                    <li><strong>Small seed packets (e.g. 50g – 500g)</strong>: Always fall within the first 1 Kg base rate (e.g. ₹50 in Punjab, ₹80 Outside Punjab).</li>
                    <li><strong>Heavier farm orders (e.g. 3.5 Kg)</strong>: Base rate (1st Kg) + 3 extra Kg = e.g., ₹50 + (3 × ₹30) = <strong>₹140</strong> in Punjab.</li>
                    <li><strong>Free Delivery</strong>: If customer subtotal meets the threshold, shipping fee automatically becomes <strong>₹0.00 (FREE)</strong>.</li>
                </ul>
            </div>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-lg text-xs shadow">
            Save Site Settings &rarr;
        </button>
    </form>
</div>

@endsection
