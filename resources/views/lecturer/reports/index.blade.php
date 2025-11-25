@extends('lecturer.dashboard')

@section('title', 'Laporan Kehadiran')
@section('page-title', 'Laporan Kehadiran Mata Kuliah')

@section('content')

{{-- FILTER PANEL --}}
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('lecturer.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

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
                <option value="">Semua</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tipe --}}
        <div>
            <label class="text-sm font-semibold text-gray-600">Tipe</label>
            <select name="learning_type"
                class="w-full mt-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua</option>
                <option value="online" {{ request('learning_type') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ request('learning_type') == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="md:col-span-4 flex justify-end gap-3 mt-3">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Filter
            </button>

            <a href="{{ route('lecturer.reports.export', request()->all()) }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                Export Excel
            </a>
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
            <tr>
                <th class="p-3">Tanggal</th>
                <th class="p-3">Mata Kuliah</th>
                <th class="p-3">Waktu</th>
                <th class="p-3">Tipe</th>
                <th class="p-3">Lokasi</th>
                <th class="p-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse($sessions as $session)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3 font-medium">
                        {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}
                    </td>

                    <td class="p-3">
                        {{ $session->course->course_name }}
                    </td>

                    <td class="p-3">
                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                    </td>

                    <td class="p-3">
                        @php
                            $isOnline = $session->learning_type === 'online';
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold
                            {{ $isOnline ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            <span class="w-3 h-3 rounded-full {{ $isOnline ? 'bg-blue-500' : 'bg-green-500' }}"></span>
                            {{ ucfirst($session->learning_type) }}
                        </span>
                    </td>

                    <td class="p-3">
                        {{ $session->location->location_name ?? '-' }}
                    </td>

                    <td class="p-3 text-center">
                        <a href="{{ route('lecturer.reports.show', $session->id) }}"
                            class="text-indigo-600 font-semibold hover:underline">
                            Detail
                        </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        Tidak ada data sesi.
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
