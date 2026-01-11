@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="max-w-md w-full">
        <!-- Header dengan Logo dan Tombol Kembali -->
        <div class="relative flex items-center justify-center mb-12">
            <!-- Tombol Kembali (Absolute Left) -->
            <a href="{{ url('/') }}" class="absolute left-0 group">
                <div class="w-12 h-12 rounded-full bg-white border-2 border-gray-200 flex items-center justify-center group-hover:border-blue-500 group-hover:shadow-lg transition-all duration-300 group-hover:scale-110">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600 transform group-hover:-translate-x-0.5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </div>
            </a>

            <!-- Logo dan Brand (Center) -->
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 flex items-center justify-center ">
                    <img src="{{ asset('images/logo-absenku.svg') }}" alt="Logo AbsenKu" class="wd-10 h-10">
                </div>
                <h1 class="text-3xl font-bold text-blue-600">AbsenKu</h1>
            </div>
        </div>

        <!-- Judul Section -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang Kembali</h2>
            <p class="text-gray-600">Masuk untuk melanjutkan ke dashboard Anda</p>
        </div>

        <!-- Form Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            @if (session('status'))
                <div class="mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <a href="{{ route('auth.google', ['intent' => 'login']) }}"
                   class="w-full inline-flex items-center justify-center gap-3 border-2 border-gray-200 rounded-xl py-3 text-sm font-semibold text-gray-700 hover:border-blue-300 hover:bg-blue-50 transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.8-5.5 3.8a6.4 6.4 0 010-12.8c1.8 0 3.1.8 3.8 1.5l2.6-2.5A9.9 9.9 0 0012 2.1 9.9 9.9 0 0012 22c5.7 0 9.5-4 9.5-9.6 0-.6-.1-1.1-.2-1.6H12z"/>
                        <path fill="#34A853" d="M3.5 7.3l3.2 2.4A6 6 0 0112 4.6c1.8 0 3.1.8 3.8 1.5l2.6-2.5A9.9 9.9 0 0012 2.1a9.9 9.9 0 00-8.5 5.2z"/>
                        <path fill="#FBBC05" d="M3.5 16.7A9.9 9.9 0 0012 22c2.7 0 5-1 6.7-2.6l-3.1-2.4c-.9.6-2 1-3.6 1a6 6 0 01-5.7-4l-2.8 2.3z"/>
                        <path fill="#4285F4" d="M21.3 12.4c0-.5-.1-1-.2-1.6H12v3.9h5.5c-.3 1.5-1.8 3.1-5.5 3.1-3.3 0-6-2.7-6-6s2.7-6 6-6c1.8 0 3.1.8 3.8 1.5l2.6-2.5A9.9 9.9 0 0012 2.1c-5.5 0-9.9 4.4-9.9 9.9s4.4 9.9 9.9 9.9c5.7 0 9.5-4 9.5-9.6z"/>
                    </svg>
                    Masuk dengan Google
                </a>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@admin.com"
                            required
                            autofocus
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 @error('email') border-red-400 @enderror"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="•••"
                            required
                            class="w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 @error('password') border-red-400 @enderror"
                        >
                        <button type="button"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition"
                                data-password-toggle="password">
                            <svg class="h-5 w-5" data-icon="show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="hidden h-5 w-5" data-icon="hide" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 transition cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <!-- Tombol Masuk -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg"
                >
                    Masuk ke Akun
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Atau</span>
                    </div>
                </div>

                <!-- Link Daftar -->
                @if (Route::has('register'))
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold transition-colors">
                                Daftar sekarang
                            </a>
                        </p>
                    </div>
                @endif
            </form>
        </div>

        <!-- Footer Text -->
        <p class="text-center text-xs text-gray-500 mt-6">
            Dengan masuk, Anda menyetujui
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Syarat & Ketentuan</a>
            dan
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Kebijakan Privasi</a>
        </p>
    </div>
</div>
@endsection
