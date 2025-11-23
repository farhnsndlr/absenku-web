@extends('layouts.dashboard')

@section('title', 'Dashboard Dosen')
@section('page-title', 'Dashboard Dosen')

{{-- ... (Bagian navigation tetap sama) ... --}}
@section('navigation')
    {{-- Menu Dashboard --}}
    <a href="{{ route('lecturer.dashboard') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg transition font-medium
       {{ request()->routeIs('lecturer.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('lecturer.dashboard') ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
        </svg>
        <span>Dashboard</span>
    </a>

    <a href="#"
       class="flex items-center gap-3 px-3 py-2 rounded-lg transition font-medium text-gray-700 hover:bg-gray-100 mt-1">
       <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <span>Kelas</span>
    </a>

    <a href="#"
       class="flex items-center gap-3 px-3 py-2 rounded-lg transition font-medium text-gray-700 hover:bg-gray-100 mt-1">
       <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <span>Laporan</span>
    </a>
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
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalCourses }}</p>
            {{-- 🔥 Teks semester dihapus, diganti teks statis --}}
            <p class="text-sm text-gray-500">Total Diampu</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            {{-- ... (ikon sama) ... --}}
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalStudents }}</p>
            <p class="text-sm text-gray-500">Total Mahasiswa Unik</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            {{-- ... (ikon sama) ... --}}
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($averageAttendance, 1) }}%</p>
            <p class="text-sm text-gray-500">Rata-rata Minggu Ini</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- ... (Kode jadwal mengajar tidak ada perubahan karena tidak pakai semester) ... --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-full">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Jadwal Mengajar Hari Ini</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($todaysSessions as $session)
                        <div class="flex gap-4 relative">
                            <div class="absolute left-[4.5rem] top-2 bottom-0 w-0.5 {{ $session->time_status == 'ongoing' ? 'bg-blue-200' : 'bg-gray-200' }} h-full -z-10"></div>
                            <div class="flex-shrink-0 w-14 text-center">
                                <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</p>
                            </div>
                            <div class="w-4 h-4 {{ $session->time_status == 'ongoing' ? 'bg-blue-500' : 'bg-gray-300' }} rounded-full border-4 border-white shadow-sm mt-1.5 z-10 relative"></div>
                            <div class="flex-1 pb-4">
                                <h4 class="text-base font-bold text-gray-900">{{ $session->course->course_name }}</h4>
                                <p class="text-sm text-gray-500 mb-3">{{ $session->course->course_code }} | {{ $session->location->location_name ?? 'Lokasi tidak ditentukan' }}</p>

                                @if($session->time_status != 'finished')
                                {{-- GANTI ROUTE INI NANTI KE ROUTE MANAJEMEN SESI YANG BENAR --}}
                                <a href="#" class="text-xs px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-sm inline-block">
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
                    {{-- GANTI ROUTE INI NANTI KE DAFTAR MATA KULIAH --}}
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">Lihat Semua</a>
                </div>
                <div class="p-6 overflow-x-auto flex-1">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-4">Mata Kuliah</th>
                                {{-- 🔥 Kolom Semester/Kelas Dihapus dari Header --}}
                                <th class="pb-4">Sesi Terakhir</th>
                                <th class="pb-4">Kehadiran</th>
                                <th class="pb-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($courseAttendanceStatus as $status)
                                <tr>
                                    <td class="py-4 pr-4">
                                        <div class="font-bold text-gray-900">{{ $status['course']->course_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $status['course']->course_code }}</div>
                                    </td>
                                    {{-- 🔥 Kolom Semester/Kelas Dihapus dari Body --}}
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
                                    <td class="py-4 text-right">
                                        {{-- GANTI ROUTE INI NANTI KE DETAIL MATA KULIAH --}}
                                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition px-3 py-2 bg-blue-50 rounded-lg">Detail</a>
                                    </td>
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
