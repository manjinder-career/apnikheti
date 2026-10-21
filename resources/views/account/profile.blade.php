@extends('layouts.app')

@section('title', 'My Profile & Address — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('account.dashboard') }}" class="hover:underline">Account</a>
            <span>&rsaquo;</span>
            <span class="font-bold text-slate-700">Profile & Default Address</span>
        </div>
        <h1 class="text-3xl font-bold text-forest-800">Account Settings</h1>
        <p class="text-xs text-slate-500 mt-1">Keep your delivery address and contact information updated for fast seed dispatches.</p>
    </div>

    <div class="bg-white rounded-3xl border border-sky-100 p-8 shadow-sm">
        <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name</label>
                        <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('name')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" required value="{{ old('phone', $user->phone) }}" placeholder="+91 98765 43210"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('phone')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" disabled value="{{ $user->email }}"
                           class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                    <span class="text-[11px] text-slate-400 mt-1 block">Email address cannot be changed as it is linked to your order history.</span>
                </div>
            </div>

            <div class="pt-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Default Delivery Address</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Street Address / Farm Location / Village</label>
                    <textarea name="address" required rows="2" placeholder="House number, Street, Village, Landmark"
                              class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">City / District</label>
                        <input type="text" name="city" required value="{{ old('city', $user->city) }}" placeholder="e.g. Ludhiana"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('city')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">State</label>
                        <input type="text" name="state" required value="{{ old('state', $user->state ?? 'Punjab') }}" placeholder="e.g. Punjab"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('state')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">PIN Code</label>
                        <input type="text" name="pincode" required value="{{ old('pincode', $user->pincode) }}" placeholder="e.g. 141001"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('pincode')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-base font-bold text-slate-900 mb-2">Change Password <span class="text-xs text-slate-400 font-normal">(Leave empty if not changing)</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Current Password</label>
                        <input type="password" name="current_password" placeholder="••••••••"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('current_password')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">New Password</label>
                        <input type="password" name="new_password" placeholder="Min. 8 characters"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600">
                        @error('new_password')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-sky-700 hover:bg-sky-800 text-white font-bold px-6 py-3 rounded-xl text-sm shadow transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
