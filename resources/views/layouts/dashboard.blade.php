<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'AbsenKu') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-absenku.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Kelas utilitas untuk menyembunyikan elemen saat Alpine memuat */
        [x-cloak] { display: none !important; }

        /* Gaya peta Leaflet */
        #map {
            height: 450px;
            width: 100%;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1rem;
            z-index: 1;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        <div class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden"
             x-show="sidebarOpen"
             x-transition.opacity
             @click="sidebarOpen = false"
             x-cloak></div>

        <aside class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full bg-white border-r border-gray-200 flex flex-col shrink-0 transition-transform duration-200 ease-out lg:static lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="h-16 flex items-center px-6 border-b border-gray-200 shrink-0">
                <a href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'lecturer' ? route('lecturer.dashboard') : route('student.dashboard'))) : route('landing') }}" class="flex items-center gap-2 px-7">
                    <img src="{{ asset('images/logo-absenku.svg') }}" alt="Logo AbsenKu" class="w-10 h-10">
                    <span class="text-xl font-bold text-blue-600">AbsenKu</span>
                </a>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                @yield('navigation')
            </nav>

            <div class="border-t border-gray-200 p-4 relative shrink-0" x-data="{ openSidebarMenu: false }">


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
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
                <div class="flex items-center gap-3">
                    <button type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 ml-4">
                    <div class="relative" x-data="{ openNotif: false }">
                        {{-- TRIGGER BUTTON --}}
                        <button @click="openNotif = !openNotif" type="button" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="sr-only">View notifications</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>

                            {{-- Badge Merah (Hanya muncul jika ada notifikasi belum dibaca) --}}
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
                            @endif
                        </button>

                        {{-- DROPDOWN CONTENT --}}
                        <div x-show="openNotif"
                            @click.away="openNotif = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none max-h-96 overflow-y-auto"
                            x-cloak>

                            <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-700">Notifikasi</span>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <a href="{{ route('notifications.readAll') }}" class="text-xs text-blue-600 hover:underline">Tandai semua dibaca</a>
                                @endif
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                @forelse(auth()->user()->notifications as $notification)
                                    <a href="{{ isset($notification->data['url']) ? $notification->data['url'] : '#' }}"
                                    class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50/30' }}">
                                        <div class="flex items-start">
                                            {{-- Ikon Berdasarkan Tipe --}}
                                            <div class="shrink-0 mr-3">
                                                @if(isset($notification->data['type']) && $notification->data['type'] === 'security_alert')
                                                    {{-- Ikon Keamanan (Warna Kuning/Merah) --}}
                                                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    </div>
                                                @else
                                                    {{-- Ikon Info Default (Warna Biru) --}}
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                {{-- Judul Notifikasi (Jika ada) --}}
                                                @if(isset($notification->data['title']))
                                                    <p class="text-sm font-semibold text-gray-900">{{ $notification->data['title'] }}</p>
                                                @endif
                                                {{-- Pesan Notifikasi --}}
                                                <p class="text-sm {{ isset($notification->data['title']) ? 'text-gray-600' : 'font-medium text-gray-900' }}">
                                                    {{ $notification->data['message'] ?? 'Notifikasi Baru' }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                        Tidak ada notifikasi.
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                    <div class="relative" x-data="{ openHeaderMenu: false }">
                        {{-- TRIGGER BUTTON --}}
                        <button @click="openHeaderMenu = !openHeaderMenu" type="button" class="flex items-center gap-2 p-1.5 hover:bg-gray-100 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="sr-only">Open user menu</span>
                            @if(auth()->user()->profile_photo_url)
                                <img class="w-8 h-8 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}?v={{ time() }}" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-semibold text-blue-600">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</span>
                                </div>
                            @endif
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

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')

    <div id="app-confirm-modal" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-gray-900/60" data-app-confirm-backdrop></div>
        <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
                <div class="flex items-start gap-3 border-b border-gray-100 px-6 py-4">
                    <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900" id="app-confirm-title">Konfirmasi</h4>
                        <p class="mt-1 text-sm text-gray-600" id="app-confirm-message">Apakah Anda yakin?</p>
                    </div>
                </div>
                <div class="hidden border-b border-gray-100 px-6 py-4" id="app-confirm-password">
                    <label for="app-confirm-password-input" class="block text-sm font-medium text-gray-700 mb-2">Password Admin</label>
                    <div class="relative">
                        <input type="password" id="app-confirm-password-input" autocomplete="current-password"
                               class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 shadow-sm py-2.5 px-4 pr-12"
                               placeholder="Masukkan password untuk konfirmasi">
                        <button type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                data-password-toggle="app-confirm-password-input">
                            <svg class="h-5 w-5" data-icon="show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="hidden h-5 w-5" data-icon="hide" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500" id="app-confirm-password-status"></p>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4">
                    <button type="button" class="rounded-md border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" id="app-confirm-cancel">Batal</button>
                    <button type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700" id="app-confirm-approve">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cek apakah ada session 'success' atau 'error' --}}
    @if (session('success') || session('error'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)" {{-- Auto-tutup setelah 4 detik --}}
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed bottom-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
        x-cloak {{-- Mencegah kedipan saat loading --}}
    >
        <div class="p-4">
            <div class="flex items-start">
                {{-- IKON (Berubah tergantung tipe pesan) --}}
                <div class="flex-shrink-0">
                    @if (session('success'))
                        {{-- Ikon Centang Hijau untuk Sukses --}}
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        {{-- Ikon Silang Merah untuk Error --}}
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>

                {{-- KONTEN PESAN --}}
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">
                        {{ session('success') ? 'Berhasil!' : 'Terjadi Kesalahan!' }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ session('success') ?? session('error') }}
                    </p>
                </div>

                {{-- TOMBOL CLOSE MANUAL --}}
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

    <script>
        window.appConfirm = function(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('app-confirm-modal');
                if (!modal) {
                    resolve(false);
                    return;
                }

                const titleEl = document.getElementById('app-confirm-title');
                const messageEl = document.getElementById('app-confirm-message');
                const cancelBtn = document.getElementById('app-confirm-cancel');
                const approveBtn = document.getElementById('app-confirm-approve');
                const backdrop = modal.querySelector('[data-app-confirm-backdrop]');
                const passwordWrap = document.getElementById('app-confirm-password');
                const passwordInput = document.getElementById('app-confirm-password-input');
                const passwordStatus = document.getElementById('app-confirm-password-status');
                const requiresPassword = Boolean(options?.requiresPassword);
                const verifyUrl = options?.verifyUrl;
                let verifyTimer = null;

                if (titleEl) titleEl.textContent = options?.title || 'Konfirmasi';
                if (messageEl) messageEl.textContent = options?.message || 'Apakah Anda yakin?';
                if (approveBtn) approveBtn.textContent = options?.confirmText || 'Lanjutkan';

                if (passwordWrap) {
                    passwordWrap.classList.toggle('hidden', !requiresPassword);
                }
                if (passwordInput) {
                    passwordInput.value = '';
                }
                if (passwordStatus) {
                    passwordStatus.textContent = '';
                    passwordStatus.className = 'mt-2 text-xs text-gray-500';
                }
                if (approveBtn) {
                    approveBtn.disabled = requiresPassword;
                    approveBtn.classList.toggle('opacity-60', requiresPassword);
                    approveBtn.classList.toggle('cursor-not-allowed', requiresPassword);
                }

                const setApproveState = (enabled) => {
                    if (!approveBtn) return;
                    approveBtn.disabled = !enabled;
                    approveBtn.classList.toggle('opacity-60', !enabled);
                    approveBtn.classList.toggle('cursor-not-allowed', !enabled);
                };

                const verifyPassword = async (value) => {
                    if (!requiresPassword) return true;
                    if (!value) {
                        if (passwordStatus) {
                            passwordStatus.textContent = 'Password wajib diisi.';
                            passwordStatus.className = 'mt-2 text-xs text-red-600';
                        }
                        setApproveState(false);
                        return false;
                    }
                    if (!verifyUrl) {
                        setApproveState(true);
                        return true;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    try {
                        const response = await fetch(verifyUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify({ password: value })
                        });
                        const data = await response.json();
                        if (data?.valid) {
                            if (passwordStatus) {
                                passwordStatus.textContent = 'Password sesuai.';
                                passwordStatus.className = 'mt-2 text-xs text-emerald-600';
                            }
                            setApproveState(true);
                            return true;
                        }
                        if (passwordStatus) {
                            passwordStatus.textContent = 'Password tidak sesuai.';
                            passwordStatus.className = 'mt-2 text-xs text-red-600';
                        }
                        setApproveState(false);
                        return false;
                    } catch (error) {
                        if (passwordStatus) {
                            passwordStatus.textContent = 'Gagal memverifikasi password.';
                            passwordStatus.className = 'mt-2 text-xs text-red-600';
                        }
                        setApproveState(false);
                        return false;
                    }
                };

                const onPasswordInput = (event) => {
                    if (!requiresPassword) return;
                    if (verifyTimer) clearTimeout(verifyTimer);
                    verifyTimer = setTimeout(() => {
                        verifyPassword(event.target.value);
                    }, 400);
                };

                const close = (value) => {
                    modal.classList.add('hidden');
                    cancelBtn.removeEventListener('click', onCancel);
                    approveBtn.removeEventListener('click', onApprove);
                    if (backdrop) backdrop.removeEventListener('click', onBackdrop);
                    if (passwordInput) passwordInput.removeEventListener('input', onPasswordInput);
                    resolve(value);
                };

                const onCancel = () => close(false);
                const onApprove = async () => {
                    if (requiresPassword) {
                        const ok = await verifyPassword(passwordInput?.value || '');
                        if (!ok) {
                            return;
                        }
                        if (options?.form && passwordInput) {
                            let hidden = options.form.querySelector('input[name="password"]');
                            if (!hidden) {
                                hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = 'password';
                                options.form.appendChild(hidden);
                            }
                            hidden.value = passwordInput.value;
                        }
                    }
                    close(true);
                };
                const onBackdrop = () => close(false);

                cancelBtn.addEventListener('click', onCancel);
                approveBtn.addEventListener('click', onApprove);
                if (backdrop) backdrop.addEventListener('click', onBackdrop);
                if (passwordInput) passwordInput.addEventListener('input', onPasswordInput);
                modal.classList.remove('hidden');
            });
        };

        document.addEventListener('submit', async (event) => {
            const form = event.target;
            if (!form || !form.dataset || !form.dataset.confirmMessage) {
                return;
            }
            event.preventDefault();
            const proceed = await window.appConfirm({
                title: form.dataset.confirmTitle,
                message: form.dataset.confirmMessage,
                confirmText: form.dataset.confirmOk,
                requiresPassword: form.dataset.confirmRequiresPassword === '1',
                verifyUrl: form.dataset.confirmVerifyUrl,
                form
            });
            if (proceed) {
                form.submit();
            }
        }, true);

        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const inputId = button.dataset.passwordToggle;
                const input = document.getElementById(inputId);
                if (!input) return;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                const showIcon = button.querySelector('[data-icon="show"]');
                const hideIcon = button.querySelector('[data-icon="hide"]');
                if (showIcon && hideIcon) {
                    showIcon.classList.toggle('hidden', !isPassword);
                    hideIcon.classList.toggle('hidden', isPassword);
                }
            });
        });

        const scrollButton = document.getElementById('scroll-to-top');
        if (scrollButton) {
            const toggleScrollButton = () => {
                if (window.scrollY > 320) {
                    scrollButton.classList.remove('hidden');
                    scrollButton.classList.add('flex');
                } else {
                    scrollButton.classList.add('hidden');
                    scrollButton.classList.remove('flex');
                }
            };
            toggleScrollButton();
            window.addEventListener('scroll', toggleScrollButton);
            scrollButton.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    </script>

    @if(auth()->check())
        @php
            $toastNotifications = auth()->user()->unreadNotifications->take(5);
            $toastPayload = $toastNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notifikasi',
                    'message' => $notification->data['message'] ?? 'Notifikasi baru.',
                    'type' => $notification->data['type'] ?? 'info',
                    'time' => $notification->created_at->diffForHumans(),
                ];
            })->values();
        @endphp
        <script>
            window.appUnreadNotifications = @json($toastPayload);
            window.appNotificationUnreadUrl = @json(route('notifications.unread'));
            window.appNotificationMarkReadUrl = @json(route('notifications.markRead'));
        </script>
    @endif

    <div id="notification-toast-container" class="fixed right-4 top-4 z-[70] space-y-3"></div>
    <button id="scroll-to-top"
            type="button"
            class="fixed bottom-5 right-5 z-[60] hidden h-11 w-11 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <span class="sr-only">Scroll ke atas</span>
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
    </button>
    <script>
        const toastContainer = document.getElementById('notification-toast-container');
        const shownNotifications = new Set();

        function toastStyle(type) {
            switch (type) {
                case 'success':
                    return {
                        ring: 'border-emerald-200',
                        bg: 'bg-emerald-50',
                        text: 'text-emerald-700',
                        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
                    };
                case 'warning':
                    return {
                        ring: 'border-amber-200',
                        bg: 'bg-amber-50',
                        text: 'text-amber-700',
                        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                    };
                case 'error':
                    return {
                        ring: 'border-red-200',
                        bg: 'bg-red-50',
                        text: 'text-red-700',
                        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
                    };
                default:
                    return {
                        ring: 'border-blue-200',
                        bg: 'bg-blue-50',
                        text: 'text-blue-700',
                        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                    };
            }
        }

        function showToast({ title, message, type, time }, delay) {
            if (!toastContainer) return;
            const styles = toastStyle(type);
            const toast = document.createElement('div');
            toast.className = `w-80 max-w-full border ${styles.ring} ${styles.bg} rounded-xl shadow-lg p-4 flex gap-3`;
            toast.innerHTML = `
                <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-white ${styles.text}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${styles.icon}
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-900">${title}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">${time || ''}</span>
                            <button type="button" data-toast-close class="inline-flex h-6 w-6 items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-white/60 transition" aria-label="Tutup notifikasi">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">${message}</p>
                </div>
            `;
            setTimeout(() => {
                toastContainer.appendChild(toast);
                const closeButton = toast.querySelector('[data-toast-close]');
                if (closeButton) {
                    closeButton.addEventListener('click', () => toast.remove());
                }
                setTimeout(() => toast.remove(), 7000);
            }, delay);
        }

        async function markNotificationsRead(ids) {
            if (!ids.length || !window.appNotificationMarkReadUrl) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                await fetch(window.appNotificationMarkReadUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({ ids })
                });
            } catch (error) {
                // abaikan
            }
        }

        async function fetchUnreadNotifications() {
            if (!window.appNotificationUnreadUrl) return;
            try {
                const response = await fetch(window.appNotificationUnreadUrl, { method: 'GET' });
                if (!response.ok) return;
                const notifications = await response.json();
                const newIds = [];
                notifications.forEach((notification, index) => {
                    if (shownNotifications.has(notification.id)) return;
                    shownNotifications.add(notification.id);
                    newIds.push(notification.id);
                    showToast(notification, index * 400);
                });
                if (newIds.length) {
                    await markNotificationsRead(newIds);
                }
            } catch (error) {
                // abaikan
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const notifications = window.appUnreadNotifications || [];
            const initialIds = [];
            notifications.forEach((notification, index) => {
                if (shownNotifications.has(notification.id)) return;
                shownNotifications.add(notification.id);
                initialIds.push(notification.id);
                showToast(notification, index * 400);
            });
            markNotificationsRead(initialIds);
            setInterval(fetchUnreadNotifications, 20000);
        });
    </script>
</body>
</html>
