@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-3 mb-4">
                <img src="{{ asset('images/logo-absenku.svg') }}" alt="Logo AbsenKu" class="h-10 w-10">
                <h1 class="text-2xl font-bold text-blue-600">AbsenKu</h1>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
            <p class="text-gray-600">Masukkan email terdaftar untuk menerima tautan reset.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            @if (session('status'))
                <div class="mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl p-4">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-4">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="johndoe@google.com"
                        required
                        autofocus
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 @error('email') border-red-400 @enderror"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg"
                >
                    Kirim Tautan Reset
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold transition-colors">
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
