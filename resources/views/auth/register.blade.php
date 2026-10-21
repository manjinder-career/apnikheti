@extends('layouts.app')

@section('title', 'Customer Registration — Apni Kheti Apne Beej')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 md:py-16">
    <div class="bg-white p-8 md:p-10 rounded-3xl border border-sky-100 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <span class="inline-flex rounded-full bg-sky-50 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-sky-800">ਕਸਟਮਰ ਖਾਤਾ • Customer Account</span>
            <h1 class="text-3xl font-bold text-forest-800">Create Customer Account</h1>
            <p class="text-sm text-slate-500">Sign up to order traditional seeds, track your seed deliveries, and manage your shipping address.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Jaswinder Singh"
                           class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                    @error('name')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="e.g. +91 98765 43210"
                           class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                    @error('phone')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="you@example.com"
                       class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                @error('email')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="At least 8 characters"
                           class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                    @error('password')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password"
                           class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Shipping Address (Optional — can be added at checkout)</p>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Street Address / Village</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="House / Farm / Village / Post Office"
                           class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">City / District</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Ludhiana"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">State</label>
                        <input type="text" name="state" value="{{ old('state', 'Punjab') }}" placeholder="e.g. Punjab"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Postal PIN Code</label>
                        <input type="text" name="pincode" value="{{ old('pincode') }}" placeholder="e.g. 141001"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold py-3.5 rounded-xl shadow transition text-sm flex items-center justify-center space-x-2">
                <span>Create My Account & Continue</span>
                <span>&rarr;</span>
            </button>
        </form>

        <div class="border-t border-slate-100 pt-4 text-center text-sm text-slate-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-bold text-sky-700 hover:text-sky-800 hover:underline">Log in here</a>
        </div>
    </div>
</div>
@endsection
