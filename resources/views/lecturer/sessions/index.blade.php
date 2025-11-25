@extends('lecturer.dashboard')

@section('title', 'Riwayat Sesi Kelas')
@section('page-title', 'Daftar Sesi Absensi Anda')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header: Tombol Tambah --}}
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Jadwal Sesi Anda</h2>
            <a href="{{ route('lecturer.sessions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Jadwalkan Sesi Baru
            </a>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-medium uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Tanggal & Waktu</th>
                        <th class="px-6 py-3">Mata Kuliah & Topik</th>
                        <th class="px-6 py-3">Tipe & Lokasi</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- Tanggal & Waktu --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ $session->session_date->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-gray-500 text-xs">
                                    {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }} WIB
                                </div>
                            </td>
                            {{-- Mata Kuliah & Topik --}}
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $session->course->course_name }}
                                    <span class="text-gray-500 font-normal">({{ $session->course->course_code }})</span>
                                </div>
                                @if($session->topic)
                                    <div class="text-gray-500 text-xs mt-1 truncate max-w-xs">
                                        Topik: {{ $session->topic }}
                                    </div>
                                @endif
                            </td>
                            {{-- Tipe & Lokasi --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    {{-- Badge Tipe --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-fit {{ $session->session_type == 'offline' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($session->session_type) }}
                                    </span>
                                    {{-- Nama Lokasi (Jika Offline) --}}
                                    @if($session->session_type == 'offline' && $session->location)
                                        <div class="flex items-center text-sm text-gray-600 mt-1" title="{{ $session->location->location_name }}">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="truncate max-w-[150px]">
                                                {{ $session->location->location_name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($session->status === 'open')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 mr-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                        Dibuka
                                    </span>
                                @elseif($session->status === 'closed')
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Selesai
                                    </span>
                                @else
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Terjadwal
                                    </span>
                                @endif
                            </td>
                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('lecturer.sessions.show', $session->id) }}" class="text-blue-600 hover:text-blue-900">Detail & Rekap</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Anda belum membuat sesi kelas apapun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $sessions->links() }}
        </div>
    </div>
@endsection
