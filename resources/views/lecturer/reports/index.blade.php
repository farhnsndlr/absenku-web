@extends('lecturer.dashboard')

@section('title', 'Laporan Kehadiran')
@section('page-title', 'Laporan Kehadiran Mata Kuliah')

@section('content')

{{-- FILTER PANEL --}}
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('lecturer.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4"> {{-- Diubah jadi md:grid-cols-5 agar muat satu baris --}}

        {{-- Tanggal Mulai --}}
        <div>
            <label class="text-sm font-semibold text-gray-600">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="w-full mt-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Tanggal Akhir --}}
        <div>
            <label class="text-sm font-semibold text-gray-600">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="w-full mt-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Mata Kuliah --}}
        <div>
            <label class="text-sm font-semibold text-gray-600">Mata Kuliah</label>
            <select name="course_id"
                class="w-full mt-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Mata Kuliah</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nama Kelas --}}
        <div>
            <label class="text-sm font-semibold text-gray-600">Nama Kelas</label>
            <select name="class_name"
                class="w-full mt-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Kelas</option>
                @foreach($classNames as $className)
                    <option value="{{ $className }}" {{ request('class_name') == $className ? 'selected' : '' }}>
                        {{ $className }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tipe Sesi --}}
        <div>
            <label class="text-sm font-semibold text-gray-600">Tipe Sesi</label>
            <select name="learning_type"
                class="w-full mt-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Tipe</option>
                <option value="online" {{ request('learning_type') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ request('learning_type') == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="md:col-span-5 flex justify-end gap-3 mt-3 border-t pt-4"> {{-- Colspan disesuaikan jadi 5 --}}
            {{-- Tombol Export Excel --}}
            {{-- Menggunakan request()->all() agar filter yang sedang aktif ikut terbawa saat export --}}
            <a href="{{ route('lecturer.reports.export', request()->all()) }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </a>

            {{-- Tombol Filter --}}
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Terapkan Filter
            </button>
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold border-b">
            <tr>
                <th class="p-4">Tanggal & Waktu</th>
                <th class="p-4">Mata Kuliah</th>
                {{-- HEADER KOLOM BARU: Kelas --}}
                <th class="p-4">Kelas</th>
                <th class="p-4">Tipe & Lokasi</th>
                <th class="p-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y">
            @forelse($sessions as $session)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Tanggal & Waktu --}}
                    <td class="p-4">
                        <div class="font-medium text-gray-900">
                            {{ $session->session_date->translatedFormat('d M Y') }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }} WIB
                        </div>
                    </td>

                    {{-- Mata Kuliah --}}
                    <td class="p-4">
                        <div class="font-medium text-gray-900">{{ $session->course->course_name }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $session->course->course_code }}</div>
                    </td>

                    {{-- KOLOM BARU: Nama Kelas --}}
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $session->class_name }}
                        </span>
                    </td>

                    {{-- Tipe & Lokasi (Digabung agar lebih rapi) --}}
                    <td class="p-4">
                        <div class="flex flex-col gap-1">
                            {{-- Badge Tipe --}}
                            @php $isOnline = $session->learning_type === 'online'; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium w-fit
                                {{ $isOnline ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-blue-500' : 'bg-purple-500' }}"></span>
                                {{ ucfirst($session->learning_type) }}
                            </span>

                            {{-- Lokasi (Jika Offline) --}}
                            @if(!$isOnline)
                                <div class="text-xs text-gray-500 flex items-center mt-0.5" title="{{ $session->location->location_name ?? '-' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate max-w-[150px]">{{ $session->location->location_name ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    </td>

                    <td class="p-4 text-center">
                        <a href="{{ route('lecturer.reports.show', $session->id) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Detail
                        </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <p class="font-medium">Tidak ada data sesi yang ditemukan.</p>
                            <p class="text-sm mt-1">Coba sesuaikan filter pencarian Anda.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
<div class="mt-6">
    {{ $sessions->links() }}
</div>

@endsection
