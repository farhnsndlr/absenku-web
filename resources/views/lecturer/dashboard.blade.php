@extends('layouts.dashboard')

@section('title', 'Dashboard Dosen')
@section('page-title', 'Dashboard Dosen')

{{-- ... (Bagian navigation tetap sama) ... --}}
@section('navigation')
    <div class="space-y-1">
        {{-- Menu Dashboard --}}
        <a href="{{ route('lecturer.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all
           {{ request()->routeIs('lecturer.dashboard') ? 'text-blue-700 bg-blue-50 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-[20px] h-[20px] {{ request()->routeIs('lecturer.dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Beranda</span>
        </a>

        <a href="{{ route('lecturer.sessions.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all
           {{ request()->routeIs('lecturer.sessions.*') ? 'text-blue-700 bg-blue-50 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
           <svg class="w-[20px] h-[20px] {{ request()->routeIs('lecturer.sessions.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
           </svg>
            <span>Sesi</span>
        </a>

        <a href="{{ route('lecturer.reports.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all
           {{ request()->routeIs('lecturer.reports.*') ? 'text-blue-700 bg-blue-50 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
           <svg class="w-[20px] h-[20px] {{ request()->routeIs('lecturer.reports.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Laporan</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">Total Mata Kuliah</h3>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            {{-- Menggunakan variabel totalCourses --}}
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalCourses }}</p>
            <p class="text-sm text-gray-500">Total Diampu</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">Total Mahasiswa</h3>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-4M7 4h10m-3 12a3 3 0 11-6 0 3 3 0 016 0zM12 12V4m-2 16h4m4-12v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h4M12 12V4m-4 8h8"/></svg>
                </div>
            </div>
            {{-- Menggunakan variabel totalStudents --}}
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalStudents }}</p>
            <p class="text-sm text-gray-500">Total Mahasiswa Unik</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">Rata-rata Presensi</h3>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6"/></svg>
                </div>
            </div>
            {{-- Menggunakan variabel averageAttendance --}}
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($averageAttendance, 1) }}%</p>
            <p class="text-sm text-gray-500">Rata-rata Semua Sesi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-full">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Jadwal Mengajar Hari Ini</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($todaysSessions as $session)
                        <div class="flex gap-4 relative">
                            {{-- Menggunakan $session->time_status == 'Active' --}}
                            <div class="absolute left-[4.5rem] top-2 bottom-0 w-0.5 {{ $session->time_status == 'Active' ? 'bg-blue-200' : 'bg-gray-200' }} h-full -z-10"></div>
                            <div class="flex-shrink-0 w-14 text-center">
                                <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</p>
                            </div>
                            {{-- Menggunakan $session->time_status == 'Active' --}}
                            <div class="w-4 h-4 {{ $session->time_status == 'Active' ? 'bg-blue-500' : 'bg-gray-300' }} rounded-full border-4 border-white shadow-sm mt-1.5 z-10 relative"></div>
                            <div class="flex-1 pb-4">
                                {{-- Mengakses properti course_name secara langsung --}}
                                <h4 class="text-base font-bold text-gray-900">{{ $session->course_name }}</h4>
                                {{-- Mengakses course_code secara langsung --}}
                                <p class="text-sm text-gray-500 mb-3">{{ $session->course_code }} | Lokasi: {{ $session->location_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 mb-3">Kode Sesi: {{ 'S-' . str_pad($session->id, 5, '0', STR_PAD_LEFT) }}</p>

                                @if($session->time_status != 'Finished')
                                <a href="{{ route('lecturer.sessions.show', $session->id) }}" class="text-xs px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-sm inline-block">
                                    Kelola Sesi
                                </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada jadwal mengajar hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-full flex flex-col">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between shrink-0">
                    <h2 class="text-lg font-bold text-gray-900">Status Absensi Mata Kuliah</h2>
                    <a href="{{ route('lecturer.sessions.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">Lihat Semua</a>
                </div>
                <div class="p-6 overflow-x-auto flex-1">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-4">Mata Kuliah</th>
                                <th class="pb-4">Sesi Terakhir</th>
                                <th class="pb-4">Kehadiran</th>
                                <th class="pb-4 text-right">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            {{-- Menggunakan variabel courseAttendanceStatus --}}
                            @forelse($courseAttendanceStatus as $status)
                                <tr>
                                    <td class="py-4 pr-4">
                                        <div class="font-bold text-gray-900">{{ $status['course']->course_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $status['course']->course_code }}</div>
                                    </td>
                                    <td class="py-4 pr-4 text-sm text-gray-500">
                                        @if($status['last_session'])
                                            <div>{{ \Carbon\Carbon::parse($status['last_session']->session_date)->translatedFormat('d M Y') }}</div>
                                            <div class="text-xs">Sesi ke-{{ $status['session_count'] }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 pr-4 w-1/4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-full bg-gray-100 rounded-full h-2">
                                                @php
                                                    $percentage = $status['percentage'];
                                                    $colorClass = $percentage >= 90 ? 'bg-green-500' : ($percentage >= 70 ? 'bg-yellow-500' : 'bg-red-500');
                                                @endphp
                                                <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900">{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right"></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Belum ada mata kuliah yang diampu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
