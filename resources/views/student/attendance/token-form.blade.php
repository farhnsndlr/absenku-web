@extends('layouts.dashboard')

@section('navigation')
    @include('student.partials.navigation')
@endsection

@section('title', 'Input Token Presensi')
@section('page-title', 'Presensi Menggunakan Token')

@section('content')
<div class="flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        {{-- Icon Header --}}
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-6">
            <svg class="h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
        <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">
            Masukkan Token Kelas
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Ketik 6 karakter token yang diberikan oleh dosen Anda di kelas.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100">
            {{-- Form Mulai --}}
            <form class="space-y-6" action="{{ route('student.attendance.process') }}" method="POST" autocomplete="off">
                @csrf

                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700 sr-only">
                        Token Presensi
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        {{-- Input Token yang Besar dan Jelas --}}
                        <input id="token"
                               name="token"
                               type="text"
                               required
                               maxlength="6"
                               autofocus
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full text-center text-4xl font-mono font-bold tracking-[0.5em] border-2 border-gray-300 rounded-xl p-4 uppercase placeholder-gray-300 {{ $errors->has('token') ? 'border-red-500 text-red-900 bg-red-50' : '' }}"
                               placeholder="------"
                               {{-- Javascript kecil untuk otomatis uppercase saat ngetik --}}
                               onkeyup="this.value = this.value.toUpperCase();"
                        >
                    </div>
                    {{-- Pesan Error Validasi --}}
                    @error('token')
                        <p class="mt-2 text-center text-sm text-red-600 font-medium">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-lg font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all active:scale-[0.98]">
                        Validasi Kehadiran
                    </button>
                </div>
            </form>
            {{-- Form Selesai --}}

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Atau
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3">
                    <a href="{{ route('student.dashboard') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
