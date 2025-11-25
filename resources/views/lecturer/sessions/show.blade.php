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

{{-- Card Info Sesi --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $session->course->course_name }}</h2>
            <p class="text-sm text-gray-500">{{ $session->course->course_code }}</p>

            <div class="mt-3 text-sm text-gray-600 space-y-1">
                <p><span class="font-medium">Tanggal:</span> {{ $session->session_date->format('d M Y') }}</p>
                <p><span class="font-medium">Waktu:</span> {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</p>
                <p><span class="font-medium">Tipe Pembelajaran:</span> {{ ucfirst($session->learning_type) }}</p>
                <p><span class="font-medium">Lokasi:</span> {{ $session->location->name ?? 'Tidak ada' }}</p>
                <p><span class="font-medium">Status:</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $session->status == 'open' ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-600' }}">
                        {{ ucfirst($session->status) }}
                    </span>
                </p>
            </div>
        </div>

        <div class="flex space-x-2">
            <a href="{{ route('lecturer.sessions.edit', $session->id) }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                Edit
            </a>

            <form method="POST" action="{{ route('lecturer.sessions.destroy', $session->id) }}">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Yakin ingin menghapus sesi ini?')"
                        class="px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition shadow-sm">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Tabel Presensi --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Mahasiswa Hadir</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">
                    <th class="py-3">Nama</th>
                    <th class="py-3">NPM</th>
                    <th class="py-3">Waktu Presensi</th>
                    <th class="py-3">Foto Bukti</th>
                    <th class="py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-700">
                @forelse ($session->attendanceRecords as $record)
                <tr>
                    <td class="py-3 font-medium">{{ $record->student->name }}</td>
                    <td class="py-3">{{ $record->student->npm }}</td>
                    <td class="py-3">{{ \Carbon\Carbon::parse($record->submission_time)->format('H:i:s') }}</td>
                    <td class="py-3">
                        @if($record->photo_path)
                            <img src="{{ $record->photo_path }}" onclick="openPhoto('{{ $record->photo_path }}')"
                                class="w-14 h-14 object-cover rounded-lg cursor-pointer border hover:shadow-md transition" />
                        @else
                            <span class="text-gray-400 text-xs">Tidak ada</span>
                        @endif
                    </td>
                    <td class="py-3">{{ ucfirst($record->status) }}</td>
                </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-gray-500 text-center">Belum ada presensi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Zoom Foto --}}
<div id="photoModal" class="fixed hidden inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center">
    <img id="photoModalImg" class="max-w-[90%] max-h-[90%] rounded-lg shadow-lg">
</div>

<script>
function openPhoto(url) {
    document.getElementById('photoModalImg').src = url;
    document.getElementById('photoModal').classList.remove('hidden');
}
document.getElementById('photoModal').onclick = () => {
    document.getElementById('photoModal').classList.add('hidden');
};
</script>

@endsection
