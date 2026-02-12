@extends('admin.dashboard')

@section('title', 'Manajemen Lokasi Kampus')
@section('page-title', 'Daftar Lokasi Geofence')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header & Tombol Tambah --}}
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Data Master Lokasi</h2>
            <a href="{{ route('admin.locations.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Lokasi
            </a>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-medium uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama Lokasi</th>
                        <th class="px-6 py-3">Latitude / Longitude</th>
                        <th class="px-6 py-3">Radius Toleransi</th>
                        <th class="px-6 py-3">Dibuat</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($locations as $location)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $location->location_name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-mono text-gray-600">Lat: {{ $location->latitude }}</div>
                                <div class="text-xs font-mono text-gray-600">Lon: {{ $location->longitude }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <span class="font-bold text-lg text-green-600">{{ $location->radius_meters }}</span> m
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $location->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.locations.edit', $location->id) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- Tombol Delete --}}
                                    <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST"
                                          data-confirm-title="Hapus Lokasi"
                                          data-confirm-message="Apakah Anda yakin ingin menghapus lokasi ini? Ini akan memengaruhi sesi presensi terkait."
                                          data-confirm-ok="Hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada data lokasi Geofence yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $locations->links() }}
        </div>
    </div>
@endsection
