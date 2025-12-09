@extends('layouts.dashboard')

@section('title', 'Input Token Presensi')
@section('page-title', 'Presensi Menggunakan Token')

@section('content')
<div class="flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-6">
            <svg class="h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
        <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">
            Check-In Kehadiran
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Masukkan token kelas dan upload foto bukti kehadiran Anda.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100">

            {{-- Tampilkan Error Global (misal: Lokasi tidak ditemukan untuk sesi offline) --}}
            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- FORM MULAI --}}
            {{-- PENTING: enctype="multipart/form-data" wajib untuk upload foto --}}
            <form class="space-y-6" action="{{ route('student.attendance.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 1. INPUT TOKEN --}}
                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700 sr-only">Token Presensi</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input id="token" name="token" type="text" required maxlength="6" autofocus
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full text-center text-4xl font-mono font-bold tracking-[0.5em] border-2 border-gray-300 rounded-xl p-4 uppercase placeholder-gray-300 {{ $errors->has('token') ? 'border-red-500 text-red-900 bg-red-50' : '' }}"
                               placeholder="------" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                    @error('token')
                        <p class="mt-2 text-center text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. UPLOAD FOTO BUKTI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti (Selfie di Kelas/Zoom) <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition relative bg-gray-50" id="dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="proof_photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span class="px-2">Pilih file foto</span>
                                    <input id="proof_photo" name="proof_photo" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(event)">
                                </label>
                                <p class="pl-1">atau drag ke sini</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG up to 3MB</p>
                        </div>
                    </div>
                    {{-- Container Preview Image --}}
                    <div id="imagePreviewContainer" class="mt-4 hidden">
                        <p class="text-sm text-gray-700 mb-2 font-medium">Preview:</p>
                        <img id="imagePreview" src="#" alt="Preview Fotomu" class="w-full h-56 object-cover rounded-lg border-2 border-blue-200 shadow-sm">
                    </div>
                    @error('proof_photo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- HIDDEN INPUTS UNTUK LOKASI --}}
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                {{-- Status Deteksi Lokasi (Hanya Info Visual) --}}
                <div id="locationStatus" class="text-sm text-gray-500 flex items-center bg-blue-50 p-3 rounded-lg">
                    <svg class="w-4 h-4 mr-2 animate-spin text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Mencoba mendeteksi lokasi Anda (diperlukan untuk sesi Offline)...</span>
                </div>

                {{-- TOMBOL SUBMIT (TIDAK PERNAH DISABLED) --}}
                <div>
                    <button type="submit" id="submitBtn" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-lg font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all active:scale-[0.98]">
                        Validasi & Kirim
                    </button>
                </div>
            </form>
            {{-- FORM SELESAI --}}

            <div class="mt-6 grid grid-cols-1 gap-3">
                <a href="{{ route('student.dashboard') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. Script Preview Foto
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewContainer').classList.remove('hidden');
                document.getElementById('dropzone').classList.add('border-blue-400', 'bg-blue-50'); // Visual feedback
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // 2. Script Geolocation (Tidak memblokir tombol submit)
    document.addEventListener('DOMContentLoaded', function() {
        const locationStatus = document.getElementById('locationStatus');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                // Sukses
                function(position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    locationStatus.innerHTML = '<span class="text-green-600 flex items-center font-medium"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Lokasi berhasil didapatkan. Siap untuk sesi Offline.</span>';
                    locationStatus.classList.remove('bg-blue-50');
                    locationStatus.classList.add('bg-green-50');
                },
                // Gagal (User menolak, timeout, dll)
                function(error) {
                    console.warn("Geolocation error:", error.message);
                    locationStatus.innerHTML = '<span class="text-orange-600 flex items-center"><svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> <span>Gagal mendapatkan lokasi. Jika ini sesi OFFLINE, presensi akan ditolak server. Pastikan GPS aktif.</span></span>';
                     locationStatus.classList.remove('bg-blue-50');
                     locationStatus.classList.add('bg-orange-50');
                     // KITA TIDAK MENDISABLE TOMBOL SUBMIT. Biarkan server yang menolak jika perlu.
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
            );
        } else {
             locationStatus.innerHTML = '<span class="text-red-600">Browser Anda tidak mendukung fitur lokasi.</span>';
        }
    });
</script>
@endpush
@endsection
