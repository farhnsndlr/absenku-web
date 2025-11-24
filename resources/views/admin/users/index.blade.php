@extends('admin.dashboard') {{-- Mewarisi layout dashboard admin --}}

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Daftar Pengguna')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header: Pencarian & Tombol Tambah --}}
        <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-4">
            {{-- Form Pencarian & Filter (GET request ke halaman ini lagi) --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-1 gap-2">
                {{-- Filter Role --}}
                <select name="role" class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="lecturer" {{ request('role') == 'lecturer' ? 'selected' : '' }}>Dosen</option>
                    <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Mahasiswa</option>
                </select>
                {{-- Input Search --}}
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 pl-10 text-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute top-2.5 left-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Cari</button>
            </form>

            {{-- Tombol Tambah --}}
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengguna
            </a>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-medium uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Nama / Email</th>
                        <th class="px-6 py-3">Role & ID</th>
                        <th class="px-6 py-3">Bergabung</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar / Inisial --}}
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{-- Badge Role --}}
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' :
                                      ($user->role === 'lecturer' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                                {{-- NID/NPM jika ada --}}
                                @if($user->profile)
                                    <p class="text-xs text-gray-500 mt-1">
                                        ID: {{ $user->profile->nid ?? $user->profile->npm ?? '-' }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $user->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- Tombol Delete (Perlu Form & Konfirmasi) --}}
                                    @if(auth()->user()->id !== $user->id) {{-- Mencegah hapus diri sendiri --}}
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Data profil terkait juga akan dihapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
@endsection
