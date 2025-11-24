{{-- Mewarisi layout dashboard utama --}}
@extends('layouts.dashboard')

{{-- Judul Halaman --}}
@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')

{{-- Navigasi Sidebar (Khusus Mahasiswa) --}}
@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('student.dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Beranda</span>
    </a>
    <a href="{{ route('student.attendance.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('student.attendance.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
        <span>Absensi (Check-In)</span>
    </a>
    {{-- Tambahkan menu lain seperti Jadwal, Nilai, dll di sini --}}
@endsection

{{-- Konten Utama Dashboard --}}
@section('content')
    <div class="max-w-7xl mx-auto">
        {{-- Sapaan Selamat Datang --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">
                Selamat Datang, {{ auth()->user()->profile->full_name ?? auth()->user()->name }}! 👋
            </h2>
            <p class="text-gray-600">Berikut adalah ringkasan aktivitas akademik Anda.</p>
        </div>

        {{-- BAGIAN 1: KARTU STATISTIK KEHADIRAN --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Hadir</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_attendance'] }} <span class="text-sm font-normal text-gray-500">Sesi</span></h3>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Izin / Sakit</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $stats['permit'] }} <span class="text-sm font-normal text-gray-500">Sesi</span></h3>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Terlambat</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $stats['late'] }} <span class="text-sm font-normal text-gray-500">Sesi</span></h3>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Alpa</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $stats['absent'] }} <span class="text-sm font-normal text-gray-500">Sesi</span></h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- BAGIAN 2: JADWAL HARI INI (Kiri - 2/3 Lebar) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Jadwal Kuliah Hari Ini</h3>
                        <span class="text-sm text-gray-500">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($todaysSchedule as $session)
                            {{-- Logika Status Sesi --}}
                            @php
                                $now = \Carbon\Carbon::now();
                                // Cek record absensi mahasiswa ini di sesi ini (menggunakan relasi yang sudah difilter di controller)
                                $myRecord = $session->records->first();

                                $statusText = '';
                                $statusClass = '';

                                if ($myRecord) {
                                    // Sudah Absen
                                    $statusText = 'Sudah Absen (' . ucfirst($myRecord->status) . ')';
                                    $statusClass = 'bg-green-100 text-green-800';
                                } elseif ($now > $session->end_time) {
                                    // Sesi Berakhir & Belum Absen
                                    $statusText = 'Sesi Berakhir (Alpa)';
                                    $statusClass = 'bg-red-100 text-red-800';
                                } elseif ($now >= $session->start_time && $now <= $session->end_time) {
                                    // Sesi Sedang Berlangsung
                                    $statusText = 'Sesi Berlangsung - Belum Absen';
                                    $statusClass = 'bg-blue-100 text-blue-800 animate-pulse';
                                } else {
                                    // Sesi Belum Dimulai
                                    $statusText = 'Akan Datang';
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp

                            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="font-bold text-gray-900">{{ $session->course->course_name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p>
                                            <svg class="w-4 h-4 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                                        </p>
                                        <p>
                                            <svg class="w-4 h-4 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $session->location->location_name ?? 'Lokasi Online' }}
                                            ({{ ucfirst($session->session_type) }})
                                        </p>
                                        <p>
                                            <svg class="w-4 h-4 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Dosen: {{ $session->course->lecturer->full_name ?? 'Nama Dosen Tidak Tersedia' }}
                                        </p>
                                    </div>
                                </div>
                                {{-- Tombol Aksi (Jika sesi berlangsung dan belum absen) --}}
                                @if($now >= $session->start_time && $now <= $session->end_time && !$myRecord)
                                    <div class="mt-4 sm:mt-0 sm:ml-4 shrink-0">
                                        <a href="{{ route('student.attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none transition shadow-sm">
                                            Check-In Sekarang
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="font-medium">Tidak ada jadwal kuliah hari ini.</p>
                                <p class="text-sm mt-1">Nikmati hari libur Anda!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- BAGIAN 3: RIWAYAT ABSENSI TERAKHIR (Kanan - 1/3 Lebar) --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Riwayat Terakhir</h3>
                        {{-- Link ke halaman riwayat lengkap (jika ada nanti) --}}
                        {{-- <a href="#" class="text-sm text-blue-600 hover:underline">Lihat Semua</a> --}}
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentHistory as $record)
                            @php
                                $statusColor = match($record->status) {
                                    'present' => 'text-green-600 bg-green-100',
                                    'late' => 'text-orange-600 bg-orange-100',
                                    'permit', 'sick' => 'text-yellow-600 bg-yellow-100',
                                    'absent' => 'text-red-600 bg-red-100',
                                    default => 'text-gray-600 bg-gray-100',
                                };
                            @endphp
                            <div class="p-4 flex items-start hover:bg-gray-50 transition">
                                {{-- Ikon Status --}}
                                <div class="shrink-0 mr-3 mt-1">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $statusColor }}">
                                        @if($record->status === 'present')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($record->status === 'late')
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif(in_array($record->status, ['permit', 'sick']))
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @else {{-- Absent --}}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $record->session->course->course_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($record->submission_time)->translatedFormat('d M Y, H:i') }}
                                        <span class="capitalize ml-1 font-medium {{ str_replace('bg-', 'text-', $statusColor) }}">
                                            ({{ $record->status }})
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500">
                                Belum ada riwayat absensi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
