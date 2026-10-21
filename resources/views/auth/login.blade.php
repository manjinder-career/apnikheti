@extends('layouts.app')

@section('title', 'Sign In — Apni Kheti Apne Beej')

@section('content')

<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white p-8 rounded-3xl border border-sky-100 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <span class="inline-flex rounded-full bg-sky-50 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-sky-800">ਖਾਤਾ ਲੌਗਇਨ • Account Sign In</span>
            <h1 class="text-2xl md:text-3xl font-bold text-forest-800">Welcome Back</h1>
            <p class="text-xs text-slate-500">Sign in to track orders, manage purchases, or access the admin panel.</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="you@example.com"
                       class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
                @error('email')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-sky-700 focus:ring-sky-500">
                    <span>Remember me on this device</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold py-3.5 rounded-xl shadow transition text-sm flex items-center justify-center space-x-2">
                <span>Sign In</span>
                <span>&rarr;</span>
            </button>
        </form>

        <div class="border-t border-slate-100 pt-4 text-center text-xs text-slate-600 space-y-3">
            <p>
                Don't have a customer account?
                <a href="{{ route('register') }}" class="font-bold text-sky-700 hover:text-sky-800 hover:underline">Register here</a>
            </p>
            <div class="bg-stone-50 p-3 rounded-xl text-[11px] text-gray-500 text-left border border-stone-200/70">
                <strong>Admin Team Demo Credentials:</strong><br>
                Email: <code class="bg-gray-200 px-1 rounded">admin@apnikheti.org</code> | Password: <code class="bg-gray-200 px-1 rounded">password</code>
            </div>
        </div>
    </div>
</div>

@endsection
