<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'AbsenKu') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-absenku.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tambahkan Alpine.js CDN (Hapus jika sudah ada di app.js) --}}
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Utility class untuk menyembunyikan elemen saat Alpine memuat */
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-200 shrink-0">
                <a href="/" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-absenku.png') }}" alt="Logo AbsenKu" class="w-10 h-10">
                    <span class="text-xl font-bold text-blue-600">AbsenKu</span>
                </a>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                @yield('navigation')
            </nav>

            <div class="border-t border-gray-200 p-4 relative shrink-0" x-data="{ openSidebarMenu: false }">
                {{-- TRIGGER BUTTON --}}
                <button @click="openSidebarMenu = !openSidebarMenu" type="button" class="w-full flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                        <span class="text-sm font-semibold text-blue-600">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0 overflow-hidden">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ ucfirst(auth()->user()->role ?? 'User') }}</p>
                    </div>
                    {{-- Ikon Chevron Berputar --}}
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': openSidebarMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- DROPDOWN MENU CONTENT --}}
                <div x-show="openSidebarMenu"
                     @click.away="openSidebarMenu = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute bottom-full left-0 w-[calc(100%-2rem)] mx-4 mb-2 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-20"
                     x-cloak> {{-- x-cloak mencegah kedipan --}}

                    <div class="py-1">
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengaturan
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 truncate">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-4 ml-4">
                    <button type="button" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="sr-only">View notifications</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <div class="relative" x-data="{ openHeaderMenu: false }">
                        {{-- TRIGGER BUTTON --}}
                        <button @click="openHeaderMenu = !openHeaderMenu" type="button" class="flex items-center gap-2 p-1.5 hover:bg-gray-100 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="sr-only">Open user menu</span>
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</span>
                            </div>
                            {{-- Ikon Chevron Berputar --}}
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{'rotate-180': openHeaderMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- DROPDOWN MENU CONTENT --}}
                        <div x-show="openHeaderMenu"
                             @click.away="openHeaderMenu = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                             x-cloak>

                            {{-- Header Dropdown (Nama User) --}}
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pengaturan</a>
                            <div class="border-t border-gray-100 my-1"></div>

                            {{-- Tombol Logout --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
