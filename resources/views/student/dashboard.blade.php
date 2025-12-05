{{-- Mewarisi layout dashboard utama --}}
@extends('layouts.dashboard')

{{-- Judul Halaman --}}
@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')

{{-- Navigasi Sidebar (Khusus Mahasiswa) --}}
@section('navigation')
    <div class="space-y-1">
        <a href="{{ route('student.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all
           {{ request()->routeIs('student.dashboard') ? 'text-blue-700 bg-blue-50 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-[20px] h-[20px] {{ request()->routeIs('student.dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Beranda</span>
        </a>
        <a href="{{ route('student.attendance.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all
           {{ request()->routeIs('student.attendance.*') ? 'text-blue-700 bg-blue-50 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-[20px] h-[20px] {{ request()->routeIs('student.attendance.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            <span>Absensi (Check-In)</span>
        </a>
    </div>
@endsection

{{-- Konten Utama Dashboard --}}
@section('content')
    {{-- Sapaan Selamat Datang --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">
            Selamat Datang, {{ auth()->user()->name }}! 👋
        </h2>
        <p class="text-gray-600 mt-1">Berikut adalah ringkasan aktivitas akademik Anda semester ini.</p>
    </div>

    {{-- BAGIAN 1: KARTU STATISTIK KEHADIRAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Card Total Hadir --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden transition hover:shadow-md">
            <div class="absolute top-4 right-4 text-green-500 bg-green-50 p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Hadir</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_attendance'] }}</h3>
            <p class="text-xs text-gray-400 mt-1">Sesi pertemuan</p>
        </div>

        {{-- Card Izin/Sakit --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden transition hover:shadow-md">
             <div class="absolute top-4 right-4 text-yellow-500 bg-yellow-50 p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Izin / Sakit</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['permit'] }}</h3>
            <p class="text-xs text-gray-400 mt-1">Sesi pertemuan</p>
        </div>

        {{-- Card Terlambat --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden transition hover:shadow-md">
             <div class="absolute top-4 right-4 text-orange-500 bg-orange-50 p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Terlambat</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['late'] }}</h3>
            <p class="text-xs text-gray-400 mt-1">Sesi pertemuan</p>
        </div>

        {{-- Card Alpa --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden transition hover:shadow-md">
             <div class="absolute top-4 right-4 text-red-500 bg-red-50 p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Alpa (Tanpa Keterangan)</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stats['absent'] }}</h3>
            <p class="text-xs text-gray-400 mt-1">Sesi pertemuan</p>
        </div>
    </div>

    {{-- BAGIAN 2: MATA KULIAH YANG DIAMBIL --}}
    @if(isset($additionalData['courses_enrolled']) && $additionalData['courses_enrolled']->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Mata Kuliah yang Diambil</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Daftar mata kuliah dan kelas yang Anda ikuti semester ini.</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                    {{ $additionalData['courses_enrolled']->count() }} Mata Kuliah
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Kode MK</th>
                            <th class="px-6 py-4">Nama Mata Kuliah</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4">Dosen Pengampu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach($additionalData['courses_enrolled'] as $course)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-medium text-indigo-700">
                                    {{ $course->course_code }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $course->course_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($course->pivot->class_name)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $course->pivot->class_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($course->lecturer && $course->lecturer->profile)
                                        <div class="font-medium text-gray-900">{{ $course->lecturer->profile->full_name }}</div>
                                    @else
                                        <span class="text-gray-400 italic">Belum ditentukan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- BAGIAN 3: JADWAL HARI INI (Kiri - 2/3 Lebar) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                         <h3 class="text-lg font-bold text-gray-900">Jadwal Kuliah Hari Ini</h3>
                         <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
                <div class="divide-y divide-gray-100 flex-1">
                    @forelse($todaysSchedule as $session)
                        {{-- Logika Status Sesi --}}
                        @php
                            $now = \Carbon\Carbon::now();
                            $myRecord = $session->records->first(); // Record absensi user yg login
                            $startTime = \Carbon\Carbon::parse($session->start_time);
                            $endTime = \Carbon\Carbon::parse($session->end_time);

                            $isOngoing = $now >= $startTime && $now <= $endTime;
                            $isFinished = $now > $endTime;

                            $statusBadge = ['text' => '', 'class' => ''];

                            if ($myRecord) {
                                $statusBadge = ['text' => 'Sudah Absen', 'class' => 'bg-green-100 text-green-800 border-green-200'];
                            } elseif ($isFinished) {
                                $statusBadge = ['text' => 'Terlewat (Alpa)', 'class' => 'bg-red-100 text-red-800 border-red-200'];
                            } elseif ($isOngoing) {
                                $statusBadge = ['text' => 'Sedang Berlangsung', 'class' => 'bg-blue-100 text-blue-800 border-blue-200 animate-pulse'];
                            } else {
                                $statusBadge = ['text' => 'Akan Datang', 'class' => 'bg-gray-100 text-gray-800 border-gray-200'];
                            }
                        @endphp

                        <div class="p-6 hover:bg-gray-50/80 transition">
                            <div class="flex flex-col sm:flex-row justify-between gap-4 sm:gap-6">
                                {{-- Kolom Waktu --}}
                                <div class="sm:w-1/4 min-w-[120px]">
                                    <div class="flex items-center text-gray-900 font-semibold">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}
                                    </div>
                                    <div class="mt-2">
                                         <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusBadge['class'] }}">
                                            {{ $statusBadge['text'] }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Kolom Detail Mata Kuliah --}}
                                <div class="flex-1">
                                    <h4 class="text-base font-bold text-gray-900 mb-2">{{ $session->course->course_name }}</h4>
                                    <div class="text-sm text-gray-600 space-y-1.5">
                                        <p class="flex items-center">
                                            @if($session->session_type == 'online')
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                            @else
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            @endif
                                            <span class="truncate">{{ $session->location->location_name ?? 'Lokasi Daring' }}</span>
                                            <span class="ml-2 text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-500 uppercase">{{ $session->session_type }}</span>
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span class="truncate">{{ $session->course->lecturer->full_name ?? 'Dosen Pengampu' }}</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Tombol Aksi (Hanya jika berlangsung & belum absen) --}}
                                @if($isOngoing && !$myRecord)
                                    <div class="sm:self-center shrink-0">
                                        <a href="{{ route('student.attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-sm">
                                            Check-In Sekarang
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center h-full">
                             <div class="bg-gray-100 p-4 rounded-full mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                             </div>
                            <h3 class="text-lg font-medium text-gray-900">Tidak Ada Jadwal</h3>
                            <p class="text-sm text-gray-500 mt-1 max-w-xs">Anda tidak memiliki jadwal kuliah hari ini. Silakan cek kembali besok.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- BAGIAN 4: RIWAYAT ABSENSI TERAKHIR (Kanan - 1/3 Lebar) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Terakhir</h3>
                    {{-- Link ke halaman riwayat lengkap (opsional) --}}
                    {{-- <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">Lihat Semua</a> --}}
                </div>
                <div class="divide-y divide-gray-100 flex-1">
                    @forelse($recentHistory as $record)
                        @php
                            // Mapping status dan style
                            $statusMap = [
                                'present' => ['label' => 'Hadir', 'icon' => 'M5 13l4 4L19 7', 'color' => 'text-green-600 bg-green-100'],
                                'late' => ['label' => 'Terlambat', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-orange-600 bg-orange-100'],
                                'permit' => ['label' => 'Izin', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-yellow-600 bg-yellow-100'],
                                'sick' => ['label' => 'Sakit', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color' => 'text-yellow-600 bg-yellow-100'],
                                'absent' => ['label' => 'Alpa', 'icon' => 'M6 18L18 6M6 6l12 12', 'color' => 'text-red-600 bg-red-100'],
                            ];
                            $statusData = $statusMap[$record->status] ?? ['label' => $record->status, 'icon' => '', 'color' => 'text-gray-600 bg-gray-100'];
                        @endphp
                        <div class="p-4 hover:bg-gray-50/80 transition flex items-center gap-4">
                            {{-- Ikon Status --}}
                            <div class="shrink-0">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl {{ $statusData['color'] }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusData['icon'] }}"/></svg>
                                </span>
                            </div>
                            {{-- Detail --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate leading-tight">
                                    {{ $record->session->course->course_name ?? 'Mata Kuliah Dihapus' }}
                                </p>
                                <div class="flex items-center mt-1 text-xs text-gray-500">
                                     <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ \Carbon\Carbon::parse($record->submission_time)->translatedFormat('d M Y, H:i') }}
                                </div>
                            </div>
                             {{-- Label Status --}}
                            <div class="shrink-0 text-xs font-medium {{ str_replace('bg-', 'text-', $statusData['color']) }}">
                                {{ $statusData['label'] }}
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center h-full text-gray-500">
                             <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <p class="text-sm">Belum ada riwayat absensi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
