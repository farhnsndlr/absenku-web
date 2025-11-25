@extends('layouts.dashboard')

@section('title', 'Detail Sesi Presensi')
@section('page-title', 'Detail Sesi Presensi')

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
