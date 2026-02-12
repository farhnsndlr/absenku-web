@extends('admin.dashboard')

@section('title', 'Manajemen Mata Kuliah')
@section('page-title', 'Daftar Mata Kuliah')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header: Tombol Tambah --}}
        <div class="p-4 border-b border-gray-100 flex justify-end">
            <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Mata Kuliah
            </a>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-medium uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Mata Kuliah</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Dosen Pengampu</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $course->course_code }}
                            </td>
                            <td class="px-6 py-4 text-gray-900">
                                {{ $course->course_name }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }} WIB
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{-- Menampilkan nama dosen (relasi ke model User) --}}
                                {{ $course->lecturer->name ?? 'Belum Ditentukan' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- Tombol Delete --}}
                                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                          data-confirm-title="Hapus Mata Kuliah"
                                          data-confirm-message="Apakah Anda yakin ingin menghapus mata kuliah ini?"
                                          data-confirm-ok="Hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada data mata kuliah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $courses->links() }}
        </div>
    </div>
@endsection
