@extends('layouts.dashboard')

@section('title', 'Absensi Mahasiswa')
@section('page-title', 'Absensi Hari Ini')

@section('content')
    <div class="max-w-4xl mx-auto">

        {{--
          === BAGIAN ALERT / NOTIFIKASI ===
          Menampilkan pesan sukses atau error dari controller setelah proses check-in.
        --}}
        @if (session('success'))
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Berhasil!</span> {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
             <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Gagal!</span> {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session('warning'))
             <div class="mb-6 p-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg border border-yellow-200" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Perhatian!</span> {{ session('warning') }}
                </div>
            </div>
        @endif


        {{--
          === BAGIAN DAFTAR SESI AKTIF ===
          Menampilkan sesi yang sedang berlangsung dan belum dihadiri.
        --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Sesi Absensi yang Sedang Berlangsung</h2>

            {{-- Cek apakah ada sesi aktif yang dikirim dari controller --}}
            @if($activeSessions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Looping setiap sesi aktif --}}
                    @foreach($activeSessions as $session)
                        <div class="border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition duration-200 relative overflow-hidden">
                            {{-- Badge Status "Sedang Berlangsung" --}}
                            <span class="absolute top-0 right-0 bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-bl-lg">
                                Sedang Berlangsung
                            </span>

                            {{-- Informasi Mata Kuliah & Dosen --}}
                            <div class="mb-4">
                                <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $session->course->course_name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $session->course->course_code }} •
                                    {{-- Menampilkan nama dosen (butuh relasi yang tepat di model Course) --}}
                                    Dosen: {{ $session->course->lecturer->lecturerProfile->full_name ?? $session->course->lecturer->name ?? 'Belum ditentukan' }}
                                </p>
                            </div>

                            {{-- Informasi Sesi (Waktu, Tipe, Lokasi, Deskripsi) --}}
                            <div class="space-y-2 mb-6 text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>
                                        {{-- Menggunakan Carbon untuk format waktu yang mudah dibaca --}}
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                                        ({{ \Carbon\Carbon::parse($session->end_time)->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) }})
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>
                                        {{ ucfirst($session->session_type) }} •
                                        {{ $session->location->location_name ?? 'Lokasi Online/Tidak Ditentukan' }}
                                    </span>
                                </div>
                                @if($session->description)
                                    <div class="flex items-start gap-2 mt-3 pt-3 border-t border-gray-100 text-gray-600 italic">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p>{{ $session->description }}</p>
                                    </div>
                                @endif
                            </div>

                            {{--
                              === TOMBOL CHECK-IN (HADIR) ===
                              Form POST yang mengarah ke route 'student.attendance.store'.
                              Kita kirim ID sesi ($session->id) sebagai parameter URL.
                            --}}
                            <form action="{{ route('student.attendance.store', $session->id) }}" method="POST" class="block">
                                @csrf {{-- Token keamanan wajib untuk form POST --}}
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2 shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Klik untuk Hadir (Check-In)</span>
                                </button>
                            </form>
                            <p class="text-xs text-center text-gray-500 mt-2">
                                Pastikan Anda berada di lokasi yang ditentukan (jika offline).
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 px-4 rounded-lg bg-gray-50 border border-dashed border-gray-300">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-lg font-medium text-gray-900">Tidak Ada Sesi Aktif Saat Ini</h3>
                    <p class="text-gray-500 mt-2">Anda tidak memiliki jadwal kuliah yang sedang membuka sesi absensi. Silakan periksa kembali nanti sesuai jadwal kuliah Anda.</p>
                </div>
            @endif
        </div>

    </div>
@endsection
