@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-6">
                <div class="mx-auto w-14 h-14 flex items-center justify-center">
                    <img src="{{ asset('images/logo-absenku.svg') }}" alt="Logo AbsenKu" class="w-10 h-10">
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mt-4">Verifikasi Email</h2>
                <p class="text-gray-600 mt-2">Kami sudah mengirim tautan verifikasi ke email Anda. Silakan cek inbox atau spam.</p>
            </div>

            @if (session('status'))
                <div class="mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl p-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                @csrf
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                    Kirim Ulang Tautan Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-blue-600 bg-blue-50 hover:bg-blue-100 font-semibold py-3 px-4 rounded-xl transition">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
