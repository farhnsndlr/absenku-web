@extends('lecturer.dashboard')

@section('title', 'Detail Kehadiran')
@section('page-title', 'Detail Laporan Kehadiran')

@section('content')

{{-- HEADER SESSION --}}
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-indigo-700">{{ $session->course->course_name }}</h2>
            <p class="text-gray-600 text-sm">{{ $session->course->course_code }}</p>

            <div class="mt-2 text-gray-700 text-sm">
                <p><strong>Tanggal:</strong> {{ $session->session_date }}</p>
                <p><strong>Waktu:</strong> {{ $session->start_time }} - {{ $session->end_time }}</p>
                <p><strong>Mode:</strong> {{ ucfirst($session->session_type) }}</p>
                <p><strong>Lokasi:</strong> {{ $session->location->location_name ?? 'Online' }}</p>
            </div>
        </div>

        <div class="text-right">
            <span class="px-3 py-1 rounded-lg text-sm font-semibold
                {{ $session->status == 'closed' ? 'bg-red-100 text-red-700' : ($session->status == 'open' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700') }}">
                {{ strtoupper($session->status) }}
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
                    <td class="p-3 font-medium">{{ $rec->student->npm }}</td>
                    <td class="p-3">{{ $rec->student->full_name ?? $rec->student->user->name }}</td>
                    <td class="p-3">{{ $rec->submission_time ?? '-' }}</td>
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
                            <a href="{{ asset('storage/' . $rec->photo_path) }}" target="_blank"
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
