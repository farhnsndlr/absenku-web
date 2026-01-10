@extends('lecturer.dashboard')

@section('title', 'Detail Kehadiran')
@section('page-title', 'Detail Laporan Kehadiran')

@section('content')

{{-- HEADER SESSION --}}
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
    {{-- Gunakan flex untuk menata judul dan tombol aksi --}}
    <div class="flex justify-between items-start">
        {{-- BAGIAN KIRI: Informasi Mata Kuliah & Sesi --}}
        <div>
            {{-- Judul Mata Kuliah & Kode --}}
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-indigo-700 leading-tight">{{ $session->course->course_name ?? 'Mata Kuliah Dihapus' }}</h2>
                <p class="text-gray-500 font-medium">{{ $session->course->course_code ?? '-' }}</p>
            </div>

            {{-- Detail Informasi Sesi (Grid 2 Kolom agar lebih rapi) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-sm text-gray-700">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h10"/></svg>
                    <span class="font-semibold mr-2">Kode Sesi:</span>
                    {{ 'S-' . str_pad($session->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="font-semibold mr-2">Tanggal:</span>
                    {{ \Carbon\Carbon::parse($session->session_date)->translatedFormat('l, d F Y') }}
                </div>
                <div class="flex items-center">
                     <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold mr-2">Waktu:</span>
                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                </div>
                <div class="flex items-center">
                     <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="font-semibold mr-2">Tipe:</span>
                    {{ ucfirst($session->learning_type) }}
                </div>
                <div class="flex items-center truncate" title="{{ $session->location->location_name ?? 'Online' }}">
                     <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="font-semibold mr-2 flex-shrink-0">Lokasi:</span>
                    <span class="truncate">{{ $session->location->location_name ?? ($session->learning_type == 'online' ? 'Online (Daring)' : '-') }}</span>
                </div>
                 @if($session->topic)
                 <div class="flex items-start md:col-span-2 mt-1">
                     <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    <span class="font-semibold mr-2 flex-shrink-0">Topik:</span>
                    <span>{{ $session->topic }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- BAGIAN KANAN: Tombol Aksi & Status --}}
        <div class="flex flex-col items-end gap-4">
            {{-- TOMBOL EXPORT (Dipindah ke sini) --}}
            <a href="{{ route('lecturer.reports.export', ['session_id' => $session->id]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition shadow-sm whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Sesi Ini
            </a>

            {{-- Badge Status --}}
            @php
                $statusClasses = match($session->status) {
                    'open' => 'bg-green-100 text-green-800 border-green-200',
                    'closed' => 'bg-gray-100 text-gray-800 border-gray-200',
                    default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                };
                $statusLabel = match($session->status) {
                    'open' => 'Dibuka (Open)',
                    'closed' => 'Selesai (Closed)',
                    default => 'Terjadwal (Scheduled)',
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>
</div>

{{-- STATISTICS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="p-4 bg-blue-50 rounded-lg text-center">
        <p class="text-sm text-gray-600">Total Mahasiswa</p>
        <p class="text-2xl font-bold text-blue-700">{{ $sessionStats['total_students'] }}</p>
    </div>

    <div class="p-4 bg-green-50 rounded-lg text-center">
        <p class="text-sm text-gray-600">Hadir</p>
        <p class="text-2xl font-bold text-green-700">{{ $sessionStats['present'] }}</p>
    </div>

    <div class="p-4 bg-red-50 rounded-lg text-center">
        <p class="text-sm text-gray-600">Tidak Hadir</p>
        <p class="text-2xl font-bold text-red-700">{{ $sessionStats['absent'] }}</p>
    </div>

    <div class="p-4 bg-yellow-50 rounded-lg text-center">
        <p class="text-sm text-gray-600">Sakit/Izin</p>
        <p class="text-2xl font-bold text-yellow-700">{{ $sessionStats['sick'] }}</p>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="p-3">NPM</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Waktu Presensi</th>
                <th class="p-3">Status</th>
                <th class="p-3">Bukti Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
                <tr class="border-t hover:bg-gray-50">

                    {{-- NPM: Jika student null, tampilkan '-' --}}
                    <td class="p-3 font-medium">{{ $rec->user?->npm ?? '-' }}</td>

                    {{-- Nama: Cek full_name, jika tidak ada cek user->name, jika student null tampilkan pesan --}}
                    <td class="p-3">
                        {{ $rec->user?->name ?? '<span class="text-red-500 italic">Data Mahasiswa Hilang</span>' }}
                    </td>

                    <td class="p-3">
                        {{ $rec->submission_time ? \Carbon\Carbon::parse($rec->submission_time)->format('H:i:s') : '-' }}
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-md text-sm font-semibold
                            @if($rec->status == 'present') bg-green-100 text-green-700
                            @elseif($rec->status == 'absent') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst($rec->status) }}
                        </span>
                    </td>
                    <td class="p-3">
                        @if($rec->photo_path)
                            <a href="{{ route('attendance.media', $rec->photo_path) }}" target="_blank"
                                class="text-indigo-600 font-semibold hover:underline">
                                Lihat Foto
                            </a>
                        @else
                            <span class="text-gray-400">Tidak Ada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">Belum ada data presensi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
