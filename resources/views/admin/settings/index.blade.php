@extends('layouts.admin')

@section('title', 'Site Settings')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Site Settings Management</h1>
        <p class="text-xs text-gray-500">Edit founder biography, slogans, verified seed statistics, and contact information without touching source code.</p>
    </div>

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

        <!-- 5. UPI Payment & QR Code Scanner Settings -->
        <div class="space-y-4 border-t pt-4">
            <div class="flex items-center justify-between border-b pb-1">
                <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider">5. UPI Payment & QR Code Scanner Settings</h2>
                <span class="text-[11px] text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-0.5 rounded-full">Displayed on Customer Order Pages</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Admin UPI ID / VPA *</label>
                    <input type="text" name="upi_id" value="{{ $settings['upi_id'] ?? 'apnikheti@sbi' }}" placeholder="e.g. apnikheti@sbi or 9876543210@paytm" class="w-full text-xs p-2.5 border rounded-lg font-mono">
                    <p class="text-[10px] text-gray-500 mt-1">Customers can send payments directly to this UPI address.</p>
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
                <p class="text-[10px] text-gray-500 mt-1">This important note informs customers that their order will only be dispatched upon payment verification.</p>
            </div>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-lg text-xs shadow">
            Save Site Settings &rarr;
        </button>
    </form>
</div>

@endsection
