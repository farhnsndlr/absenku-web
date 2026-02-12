@extends('lecturer.dashboard')
@section('title', 'Riwayat Sesi Kelas')
@section('page-title', 'Daftar Sesi Absensi Anda')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header: Tombol Tambah --}}
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Jadwal Sesi Anda</h2>
            <a href="{{ route('lecturer.sessions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Jadwalkan Sesi Baru
            </a>
        </div>
        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-medium uppercase tracking-wider text-xs border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Tanggal & Waktu</th>
                        <th class="px-6 py-3">Mata Kuliah & Topik</th>
                        {{-- KOLOM BARU: Nama Kelas --}}
                        <th class="px-6 py-3">Nama Kelas</th>
                        <th class="px-6 py-3">Tipe & Lokasi</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Token Presensi</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50/50 transition">
                            {{-- Tanggal & Waktu --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ $session->session_date->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-gray-500 text-xs mt-1">
                                    {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }} WIB
                                </div>
                            </td>
                            {{-- Mata Kuliah & Topik --}}
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $session->course->course_name }}
                                    <span class="text-gray-500 font-normal text-xs">({{ $session->course->course_code }})</span>
                                </div>
                                @if($session->topic)
                                    <div class="text-gray-500 text-xs mt-1 truncate max-w-xs" title="{{ $session->topic }}">
                                        Topik: {{ $session->topic }}
                                    </div>
                                @endif
                            </td>
                            {{-- Nama Kelas --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $session->class_name }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    Kode: {{ 'S-' . str_pad($session->id, 5, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            {{-- Tipe & Lokasi (Sudah diperbaiki menggunakan learning_type) --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($session->learning_type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-fit {{ $session->learning_type == 'offline' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($session->learning_type) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-red-500 italic font-medium">Tipe Belum Diatur</span>
                                    @endif

                                    @if($session->learning_type === 'offline')
                                        @if($session->location)
                                            <div class="flex items-center text-sm text-gray-600 mt-1" title="{{ $session->location->location_name }}">
                                                <svg class="w-4 h-4 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate max-w-[150px]">
                                                    {{ $session->location->location_name }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="flex items-center text-xs text-red-500 italic mt-1 font-medium">
                                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <span>Lokasi Tidak Ditemukan</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $startAt = $session->start_date_time;
                                    $endAt = $session->end_date_time;
                                    $isOngoing = $session->status === 'open' && $now->between($startAt, $endAt);
                                    $isFinished = $session->status === 'closed' || $now->greaterThan($endAt);
                                @endphp

                                @if($isOngoing)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                        Sedang Berlangsung
                                    </span>
                                @elseif($isFinished)
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        Selesai
                                    </span>
                                @elseif($session->status === 'open') {{-- Terjadwal tapi belum mulai --}}
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        Akan Datang
                                    </span>
                                @else {{-- Status lain/default --}}
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                @endif
                            </td>
                            {{-- Token Presensi --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($session->session_token)
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold tracking-[0.3em] font-mono bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $session->session_token }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('lecturer.sessions.show', $session->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold hover:underline">
                                    Detail & Rekap
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-base font-medium text-gray-900">Belum ada sesi kelas.</p>
                                    <p class="text-sm mt-1">Jadwalkan sesi absensi baru untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination yang Lebih Rapi --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
            {{ $sessions->links('pagination::tailwind') }} {{-- Menggunakan style Tailwind bawaan --}}
        </div>
    </div>
@endsection
