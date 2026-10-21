<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Apni Kheti Apne Beej — ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ')</title>
    <meta name="description" content="@yield('meta_description', 'Preserving traditional seeds, protecting agricultural biodiversity, and sharing sustainable farming knowledge in Punjab.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Gurmukhi:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine JS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: {
                            50: '#eff8ff',
                            100: '#dceffd',
                            600: '#2482b8',
                            700: '#145d8a',
                            800: '#103c5a',
                        },
                        leaf: {
                            500: '#40916c',
                            400: '#52b788',
                        },
                        earth: {
                            100: '#f7f3ef',
                            600: '#9c6644',
                            700: '#7f5539',
                            800: '#4a3222',
                        },
                        cream: '#f2f9ff',
                        mustard: '#73c8e8',
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans Gurmukhi', 'sans-serif'],
                        gurmukhi: ['Noto Sans Gurmukhi', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { background-color: #f5faff; color: #18344a; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- Top Navigation Header -->
    <header class="bg-white/95 text-forest-800 shadow-sm sticky top-0 z-50 backdrop-blur border-b border-sky-100" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Brand Name -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center text-forest-700 font-bold shadow-sm">
                        <svg class="w-8 h-8 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-lg md:text-xl font-bold font-gurmukhi tracking-wide text-forest-800">ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ</span>
                        <span class="block text-xs text-sky-700 tracking-wider font-medium uppercase">Apni Kheti Apne Beej</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden xl:flex items-center space-x-4 text-xs font-semibold">
                    <a href="{{ route('home') }}" class="hover:text-sky-600 transition {{ request()->routeIs('home') ? 'text-sky-700' : 'text-slate-600' }}">{{ __('Home') }}</a>
                    <a href="{{ route('about') }}" class="hover:text-sky-600 transition {{ request()->routeIs('about') ? 'text-sky-700' : 'text-slate-600' }}">{{ __('About') }}</a>
                    <a href="{{ route('seeds.index') }}" class="hover:text-sky-600 transition {{ request()->routeIs('seeds.*') ? 'text-sky-700' : 'text-slate-600' }}">{{ __('Seed Library') }}</a>
                    <a href="{{ route('shop.index') }}" class="hover:text-sky-600 transition {{ request()->routeIs('shop.*') ? 'text-sky-700 font-bold' : 'text-slate-600' }}">{{ __('Seeds & Kits') }}</a>
                    <a href="{{ route('orders.track') }}" class="hover:text-sky-600 transition {{ request()->routeIs('orders.track') ? 'text-sky-700 font-bold' : 'text-slate-600' }}">{{ __('Track Order') }}</a>
                    <a href="{{ route('gallery.index') }}" class="hover:text-sky-600 transition {{ request()->routeIs('gallery.*') ? 'text-sky-700' : 'text-slate-600' }}">{{ __('Photo Gallery') }}</a>
                    <a href="{{ route('videos.index') }}" class="hover:text-sky-600 transition {{ request()->routeIs('videos.*') ? 'text-sky-700' : 'text-slate-600' }}">{{ __('YouTube Videos') }}</a>
                    <a href="{{ route('organic-farming') }}" class="hover:text-sky-600 transition {{ request()->routeIs('organic-farming') ? 'text-sky-700' : 'text-slate-600' }}">{{ __('Natural Farming') }}</a>
                    <a href="{{ route('contact') }}" class="hover:text-sky-600 transition {{ request()->routeIs('contact') ? 'text-sky-700' : 'text-slate-600' }}">{{ __('Contact') }}</a>
                </nav>

                <!-- Actions: Cart, User Menu, Contribute -->
                <div class="hidden xl:flex items-center space-x-3">
                    @php
                        $cartCount = app(\App\Services\CartService::class)->count();
                    @endphp
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center space-x-1.5 bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                        <span>🛒</span>
                        <span>Cart</span>
                        @if($cartCount > 0)
                            <span class="bg-forest-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-4 text-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <div class="relative" x-data="{ userMenu: false }">
                            <button @click="userMenu = !userMenu" class="flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-800 px-3 py-2 rounded-xl text-xs font-semibold focus:outline-none">
                                <span>👤 {{ Str::limit(auth()->user()->name, 12) }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="userMenu" @click.away="userMenu = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded-2xl shadow-xl py-2 text-xs border border-sky-100 z-50">
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-sky-50 font-bold text-emerald-800">🛠️ Admin Portal</a>
                                    <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 hover:bg-sky-50 text-slate-700">📦 Manage Orders</a>
                                @else
                                    <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 hover:bg-sky-50 font-bold text-sky-800">👤 Customer Portal</a>
                                    <a href="{{ route('account.orders.index') }}" class="block px-4 py-2 hover:bg-sky-50 text-slate-700">📦 My Seed Orders</a>
                                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 hover:bg-sky-50 text-slate-700">⚙️ Edit Profile</a>
                                @endif
                                <div class="border-t border-slate-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-rose-50 text-rose-600 font-semibold">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-sky-700 px-2 py-2">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="bg-forest-700 hover:bg-forest-800 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                            Register
                        </a>
                    @endauth

                    <a href="{{ route('seeds.contribute') }}" class="bg-sky-700 hover:bg-sky-600 text-white px-3.5 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                        + {{ __('Contribute Seed') }}
                    </a>
                </div>

                <!-- Mobile Menu Button & Cart -->
                <div class="flex xl:hidden items-center space-x-2">
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center bg-sky-50 text-sky-800 border border-sky-200 px-2.5 py-1.5 rounded-lg text-xs font-bold">
                        <span>🛒</span>
                        @if($cartCount > 0)
                            <span class="ml-1 bg-forest-700 text-white text-[10px] font-bold px-1.5 rounded-full">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <button @click="open = !open" class="text-forest-700 hover:text-sky-600 focus:outline-none p-1">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="open" x-cloak class="xl:hidden bg-white border-t border-sky-100 px-4 pt-2 pb-4 grid grid-cols-2 gap-1 text-sm">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="block px-3 py-2 rounded-lg text-sky-800 font-bold hover:bg-sky-50">🛍️ {{ __('Seeds & Kits') }}</a>
            <a href="{{ route('cart.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">🛒 {{ __('Cart') }}</a>
            <a href="{{ route('orders.track') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">🚚 {{ __('Track Order') }}</a>
            <a href="{{ route('seeds.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">{{ __('Seed Library') }}</a>
            <a href="{{ route('organic-farming') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">{{ __('Natural Farming') }}</a>
            <a href="{{ route('gallery.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">{{ __('Photo Gallery') }}</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">{{ __('Contact Us') }}</a>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="col-span-2 block px-3 py-2 rounded-lg bg-emerald-50 text-emerald-800 font-bold">🛠️ Admin Portal</a>
                @else
                    <a href="{{ route('account.dashboard') }}" class="col-span-2 block px-3 py-2 rounded-lg bg-sky-50 text-sky-800 font-bold">👤 Customer Account ({{ auth()->user()->name }})</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="col-span-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-rose-600 font-semibold">Sign Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50">Sign In</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-forest-700 font-bold hover:bg-sky-50">Register</a>
            @endauth

            <a href="{{ route('seeds.contribute') }}" class="col-span-2 block px-3 py-2 rounded-lg bg-sky-700 text-white font-semibold text-center mt-2">+ {{ __('Contribute Seed') }}</a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-4 rounded shadow-sm text-sm flex justify-between items-center" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-700 hover:text-emerald-900 font-bold">&times;</button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-red-100 border-l-4 border-red-600 text-red-800 p-4 rounded shadow-sm text-sm flex justify-between items-center" x-data="{ show: true }" x-show="show">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-700 hover:text-red-900 font-bold">&times;</button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-forest-800 text-gray-300 mt-16 border-t-4 border-amber-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Column 1: Initiative info -->
                <div class="space-y-4">
                    <h3 class="text-xl font-bold font-gurmukhi text-amber-300">ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ</h3>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        Dedicated to traditional seed conservation, agricultural biodiversity preservation, and natural farming education in Punjab and North India.
                    </p>
                    <p class="text-xs text-amber-200 italic font-gurmukhi">"ਆਪਣੀ ਖੇਤੀ। ਆਪਣੇ ਬੀਜ। ਸਾਡਾ ਭਵਿੱਖ।"</p>
                </div>

                <!-- Column 2: Quick links -->
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 border-b border-forest-600 pb-2">Quick Navigation</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-amber-300 transition font-semibold text-amber-200">🌱 Seeds & Kits Shop</a></li>
                        <li><a href="{{ route('orders.track') }}" class="hover:text-amber-300 transition">🚚 Track Seed Shipment</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-amber-300 transition">🛒 Shopping Cart</a></li>
                        <li><a href="{{ route('seeds.index') }}" class="hover:text-amber-300 transition">Traditional Seeds Database</a></li>
                        <li><a href="{{ route('organic-farming') }}" class="hover:text-amber-300 transition">Natural Farming Practices</a></li>
                        <li><a href="{{ route('farmer-stories.index') }}" class="hover:text-amber-300 transition">Farmer Experiences</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-amber-300 transition">Educational Resources</a></li>
                        <li><a href="{{ route('gallery.index') }}" class="hover:text-amber-300 transition">Photo Gallery</a></li>
                    </ul>
                </div>

                <!-- Column 3: Seed Saver Contribution -->
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 border-b border-forest-600 pb-2">Seed Conservation</h4>
                    <p class="text-xs text-gray-300 mb-4">Are you preserving a traditional seed variety? Share your knowledge to help document Punjab's agricultural heritage.</p>
                    <a href="{{ route('seeds.contribute') }}" class="inline-block bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold px-4 py-2 rounded shadow transition mb-3">
                        Submit Traditional Seed
                    </a>
                    <div class="pt-2 text-xs text-slate-300 border-t border-forest-700">
                        <span>Customer Portal:</span>
                        @auth
                            <a href="{{ route('account.dashboard') }}" class="text-amber-300 underline font-semibold ml-1">My Account</a>
                        @else
                            <a href="{{ route('login') }}" class="text-amber-300 underline font-semibold ml-1">Customer Sign In</a>
                            <span class="mx-1">•</span>
                            <a href="{{ route('register') }}" class="text-amber-300 underline font-semibold">Register</a>
                        @endauth
                    </div>
                </div>

                <!-- Column 4: Newsletter -->
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 border-b border-forest-600 pb-2">Stay Updated</h4>
                    <p class="text-xs text-gray-300 mb-3">Subscribe to receive agricultural guides and seed conservation updates.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="email" name="email" required placeholder="Enter your email..." class="w-full text-xs px-3 py-2 rounded bg-forest-700 text-white border border-forest-600 focus:outline-none focus:border-amber-400">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold py-2 rounded shadow transition">Subscribe</button>
                    </form>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-forest-700 text-center text-xs text-gray-400 flex flex-col md:flex-row justify-between items-center">
                <p>&copy; {{ date('Y') }} Apni Kheti Apne Beej (ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ). All Rights Reserved.</p>
                <div class="mt-2 md:mt-0 space-x-4">
                    <a href="{{ route('shop.index') }}" class="hover:text-amber-300">Seed Shop</a>
                    <a href="{{ route('orders.track') }}" class="hover:text-amber-300">Track Order</a>
                    <a href="{{ route('login') }}" class="hover:text-amber-300">Staff & Admin Login</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
