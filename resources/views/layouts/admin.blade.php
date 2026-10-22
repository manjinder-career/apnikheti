<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel — @yield('title', 'Apni Kheti Apne Beej')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Gurmukhi:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: {
                            700: '#1b4332',
                            800: '#081c15',
                        },
                        amber: {
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 min-h-screen flex flex-col">

    <div class="flex flex-grow" x-data="{ sidebarOpen: false }">

        <!-- Admin Sidebar -->
        <aside class="w-64 bg-forest-800 text-white flex-shrink-0 hidden md:block border-r border-emerald-900">
            <div class="p-6 border-b border-emerald-900">
                <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg text-amber-300 font-gurmukhi block">ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ</a>
                <span class="text-xs text-emerald-300 uppercase tracking-wider font-semibold">Admin Portal</span>
            </div>
            <nav class="p-4 space-y-1 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.seeds.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.seeds.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Seed Library</span>
                </a>

                <a href="{{ route('admin.submissions.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.submissions.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Seed Submissions</span>
                </a>

                <a href="{{ route('admin.articles.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.articles.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span>Articles & Guides</span>
                </a>

                <a href="{{ route('admin.farmer-stories.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.farmer-stories.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Farmer Stories</span>
                </a>

                <a href="{{ route('admin.organizations.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.organizations.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Organizations</span>
                </a>

                <a href="{{ route('admin.media.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.media.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Media & Videos</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <div class="flex items-center space-x-3">
                        <span aria-hidden="true">📦</span>
                        <span>Orders</span>
                    </div>
                    @php
                        $pendingOrdersNavCount = \App\Models\Order::whereIn('status', ['pending', 'processing'])->count();
                    @endphp
                    @if($pendingOrdersNavCount > 0)
                        <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingOrdersNavCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.customers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.customers.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <span aria-hidden="true">👥</span>
                    <span>Customers</span>
                </a>

                <a href="{{ route('admin.shop-products.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.shop-products.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <span aria-hidden="true">🛍️</span>
                    <span>Shop Products</span>
                </a>

                <a href="{{ route('admin.shop-categories.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.shop-categories.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <span aria-hidden="true">🏷️</span>
                    <span>Shop Categories</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Settings & Payments</span>
                </a>

                <a href="{{ route('admin.contact-messages.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.contact-messages.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-forest-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Inquiries</span>
                </a>
            </nav>
        </aside>

        <!-- Main Admin Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header bar -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 border-b">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="text-xs text-emerald-700 hover:underline flex items-center space-x-1 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>View Public Website</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full uppercase">{{ auth()->user()->role }}</span>
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold bg-red-50 px-3 py-1.5 rounded border border-red-200">Logout</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-4 rounded mb-6 text-sm flex justify-between items-center" x-data="{ show: true }" x-show="show">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="font-bold text-lg">&times;</button>
                    </div>
                @endif

                @yield('admin_content')
            </main>
        </div>
    </div>

</body>
</html>
