@extends('layouts.dashboard')

@section('navigation')
    @include('student.partials.navigation')
@endsection

@section('title', 'Presensi Sesi')

@section('content')
<div class="max-w-md mx-auto mt-8">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-600 rounded-full mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Check-In Presensi</h2>
            <p class="text-gray-500 text-sm mt-2">Masukkan token dari dosen dan upload bukti foto.</p>
        </div>

        {{-- Tampilkan pesan error global jika ada --}}
        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM PRESENSI --}}
        <form action="{{ route('student.attendance.store') }}" method="POST" enctype="multipart/form-data" id="attendanceForm">
            @csrf

            {{-- 1. INPUT TOKEN (BARU) --}}
            <div class="mb-6">
                <label for="token" class="block text-sm font-medium text-gray-700 mb-2">Token Sesi <span class="text-red-500">*</span></label>
                <input type="text" id="token" name="token" required maxlength="6"
                       class="w-full text-center text-2xl font-mono font-bold tracking-widest uppercase py-3 border-2 rounded-xl focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('token') ? 'border-red-500' : 'border-gray-300' }}"
                       placeholder="------"
                       onkeyup="this.value = this.value.toUpperCase();">
                @error('token')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 2. UPLOAD FOTO BUKTI --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti <span class="text-red-500">*</span></label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl" id="dropzone">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="proof_photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Upload file</span>
                                <input id="proof_photo" name="proof_photo" type="file" class="sr-only" accept="image/*" required onchange="previewImage(event)">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
                {{-- Preview Image Container --}}
                <div id="imagePreviewContainer" class="mt-4 hidden">
                    <p class="text-sm text-gray-500 mb-2">Preview:</p>
                    <img id="imagePreview" src="#" alt="Preview Fotomu" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                </div>
                @error('proof_photo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- HIDDEN INPUTS UNTUK LOKASI (Akan diisi otomatis oleh JS) --}}
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            {{-- Status Lokasi (Visual feedback) --}}
            <div id="locationStatus" class="mb-4 text-sm text-gray-500 flex items-center">
                <svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Sedang mendeteksi lokasi...
            </div>

            <button type="submit" id="submitBtn" disabled
                class="w-full bg-blue-600 text-white py-3 rounded-xl text-lg font-bold hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition disabled:opacity-50 disabled:cursor-not-allowed">
                Kirim Presensi
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // 1. Script untuk pratinjau gambar
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('imagePreview');
            output.src = reader.result;
            document.getElementById('imagePreviewContainer').classList.remove('hidden');
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // 2. Script untuk geolokasi
    document.addEventListener('DOMContentLoaded', function() {
        const submitBtn = document.getElementById('submitBtn');
        const locationStatus = document.getElementById('locationStatus');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                // Sukses mendapatkan lokasi
                function(position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    locationStatus.innerHTML = '<span class="text-green-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Lokasi berhasil didapatkan.</span>';
                    submitBtn.disabled = false;
                },
                // Gagal mendapatkan lokasi
                function(error) {
                    let errorMsg = 'Gagal mendapatkan lokasi.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED: errorMsg = "Anda menolak akses lokasi. Wajib untuk sesi offline."; break;
                        case error.POSITION_UNAVAILABLE: errorMsg = "Informasi lokasi tidak tersedia."; break;
                        case error.TIMEOUT: errorMsg = "Waktu permintaan lokasi habis."; break;
                    }
                    locationStatus.innerHTML = '<span class="text-red-500 flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ' + errorMsg + '</span>';

                    submitBtn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            locationStatus.innerHTML = '<span class="text-red-500">Browser Anda tidak mendukung Geolocation.</span>';
            submitBtn.disabled = false;
        }
    });
</script>
@endpush
@endsection
