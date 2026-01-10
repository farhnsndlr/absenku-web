@extends('layouts.dashboard')
@section('title', 'Detail Sesi Presensi')
@section('page-title', 'Detail Sesi Presensi')

@section('navigation')
    {{-- Menu Dashboard --}}
    <a href="{{ route('lecturer.dashboard') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg transition font-medium text-gray-700 hover:bg-gray-100">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
        </svg>
        <span>Dashboard</span>
    </a>
    {{-- Menu Kelas (Aktif di halaman ini) --}}
    <a href="{{ route('lecturer.sessions.index') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg transition font-medium bg-blue-50 text-blue-600 mt-1">
       <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <span>Kelas</span>
    </a>
    {{-- Menu Laporan --}}
    <a href="{{ route('lecturer.reports.index') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg transition font-medium text-gray-700 hover:bg-gray-100 mt-1">
       <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span>Laporan</span>
    </a>
@endsection

@section('content')

{{-- --- TAMPILAN TOKEN PRESENSI --- --}}
@if($session->status == 'open' || $session->status == 'scheduled')
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-6 text-center text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-white opacity-5 transform -skew-x-12 z-0"></div>

        <div class="relative z-10">
            <h3 class="text-lg font-semibold mb-2 opacity-90">Token Presensi Sesi Ini</h3>
            <p class="text-sm mb-4 opacity-80">Bagikan kode ini kepada mahasiswa di kelas untuk melakukan check-in.</p>

            <div class="inline-block relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 to-pink-500 rounded-xl blur opacity-30 group-hover:opacity-50 transition duration-1000"></div>
                <div class="relative text-5xl md:text-6xl font-extrabold tracking-widest font-mono bg-white text-gray-900 px-8 py-4 rounded-lg border-4 border-blue-200/50 shadow-xl select-all">
                    {{ $session->session_token ?? 'BELUM ADA' }}
                </div>
            </div>

            <p class="text-xs mt-4 opacity-70 font-medium uppercase tracking-wider">
                Token berlaku sampai sesi berakhir pukul {{ $session->end_time->format('H:i') }} WIB
            </p>
        </div>
    </div>
@endif


{{-- Card Info Sesi --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $session->course->course_name }}</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $session->course->course_code }}</p>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-sm text-gray-600">
                <p><span class="font-medium text-gray-700">Kode Sesi:</span> {{ 'S-' . str_pad($session->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p><span class="font-medium text-gray-700">Tanggal:</span> {{ $session->session_date->format('d M Y') }}</p>
                <p><span class="font-medium text-gray-700">Waktu:</span> {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }} WIB</p>
                {{-- Tambahkan Nama Kelas --}}
                <p><span class="font-medium text-gray-700">Kelas:</span> {{ $session->class_name }}</p>
                <p><span class="font-medium text-gray-700">Tipe Pembelajaran:</span> {{ ucfirst($session->learning_type) }}</p>
                <p><span class="font-medium text-gray-700">Lokasi:</span> {{ $session->location->location_name ?? 'Online/Tidak ada' }}</p>
                <p class="flex items-center"><span class="font-medium text-gray-700 mr-2">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $session->status == 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        @if($session->status == 'open')
                            <span class="w-2 h-2 mr-1.5 bg-green-400 rounded-full animate-pulse"></span>
                        @endif
                        {{ ucfirst($session->status) }}
                    </span>
                </p>
                {{-- Tambahkan Topik jika ada --}}
                @if($session->topic)
                    <p class="md:col-span-2 mt-1"><span class="font-medium text-gray-700">Topik:</span> {{ $session->topic }}</p>
                @endif
            </div>
        </div>
        {{-- Tombol Aksi (Responsif) --}}
        <div class="flex space-x-2 shrink-0">
            <a href="{{ route('lecturer.sessions.edit', $session->id) }}"
               class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition shadow-sm border border-blue-100">
                Edit Sesi
            </a>
            <form method="POST" action="{{ route('lecturer.sessions.destroy', $session->id) }}"
                  data-confirm-title="Hapus Sesi"
                  data-confirm-message="Yakin ingin menghapus sesi ini?"
                  data-confirm-ok="Hapus">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition shadow-sm border border-red-100">
                    Hapus Sesi
                </button>
            </form>
        </div>
    </div>
</div>


{{-- Tabel Presensi --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 overflow-hidden">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-900">Daftar Kehadiran Mahasiswa</h3>
        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
            Total Hadir: {{ $session->attendanceRecords->count() }}
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Nama Mahasiswa</th>
                    <th class="px-6 py-3">NPM</th>
                    <th class="px-6 py-3">Waktu Presensi</th>
                    <th class="px-6 py-3">Foto Bukti</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse ($session->attendanceRecords as $record)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $record->student->user->name ?? $record->student->full_name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $record->student->npm ?? '-' }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($record->submission_time)->format('H:i:s') }} WIB</td>
                    <td class="px-6 py-4">
                        @if($record->photo_path)
                            <div class="relative group w-14 h-14 rounded-lg overflow-hidden cursor-pointer shadow-sm border border-gray-200 hover:shadow-md transition" onclick="openPhoto('{{ route('attendance.media', $record->photo_path) }}')">
                                <img src="{{ route('attendance.media', $record->photo_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition duration-300"></div>
                            </div>
                        @else
                            <span class="text-gray-400 text-xs italic">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusClasses = [
                                'present' => 'bg-green-100 text-green-800',
                                'late' => 'bg-yellow-100 text-yellow-800',
                                'permit' => 'bg-blue-100 text-blue-800',
                                'sick' => 'bg-blue-100 text-blue-800',
                                'absent' => 'bg-red-100 text-red-800',
                            ];
                            $statusClass = $statusClasses[$record->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50 italic">
                            Belum ada data presensi untuk sesi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Zoom Foto --}}
<div id="photoModal" class="fixed hidden inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300">
    <img id="photoModalImg" class="max-w-full max-h-full rounded-xl shadow-2xl transform scale-95 transition-transform duration-300">
</div>

<script>
    const modal = document.getElementById('photoModal');
    const modalImg = document.getElementById('photoModalImg');

    function openPhoto(url) {
        modalImg.src = url;
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalImg.classList.remove('scale-95');
        }, 10);
    }

    modal.onclick = () => {
        modalImg.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    };
</script>
@endsection
