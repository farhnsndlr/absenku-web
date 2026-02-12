@extends('admin.dashboard')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Data Pengguna')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8"> {{-- Padding container diperbesar --}}
        {{-- Inisialisasi Alpine dengan role user yang sedang diedit --}}
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-8" x-data="{ role: '{{ $user->role }}' }">
            @csrf
            @method('PUT') {{-- Method PUT untuk update --}}

            {{-- BAGIAN 1: DATA AKUN --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4 mb-6">Data Akun</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Password Baru --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        {{-- Wrapper Relative dengan Alpine Data --}}
                        <div class="relative" x-data="{ show: false }">
                            <input
                                :type="show ? 'text' : 'password'"
                                name="password"
                                id="password"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4 pr-10"
                                placeholder="(Kosongkan jika tidak diubah)"
                            >
                            {{-- Tombol Toggle Ikon --}}
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        {{-- Wrapper Relative dengan Alpine Data --}}
                        <div class="relative" x-data="{ show: false }">
                            <input
                                :type="show ? 'text' : 'password'"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4 pr-10"
                            >
                            {{-- Tombol Toggle Ikon --}}
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Role (Disabled agar tidak bisa diubah) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Peran Pengguna</label>
                    <select disabled class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm py-2.5 px-4">
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="lecturer" {{ $user->role === 'lecturer' ? 'selected' : '' }}>Dosen</option>
                        <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                    {{-- Input hidden untuk tetap mengirim data role --}}
                    <input type="hidden" name="role" value="{{ $user->role }}">
                </div>
            </div>

            {{-- BAGIAN 2: FIELDSET DINAMIS (Profil) --}}

            {{-- Jika Role Dosen --}}
            <div x-show="role === 'lecturer'" x-cloak class="border-t border-gray-100 pt-6">
                 <h3 class="text-lg font-bold text-gray-900 mb-6">Data Dosen</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nid" class="block text-sm font-medium text-gray-700 mb-2">NID <span class="text-red-500">*</span></label>
                        <input type="text" name="nid" id="nid" value="{{ old('nid', $user->profile->nid ?? '') }}" :required="role === 'lecturer'" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                        @error('nid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                     <div>
                        <label for="phone_number_lecturer" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" name="phone_number" id="phone_number_lecturer" value="{{ old('phone_number', $user->profile->phone_number ?? '') }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                        @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Jika Role Mahasiswa --}}
            <div x-show="role === 'student'" x-cloak class="border-t border-gray-100 pt-6">
                 <h3 class="text-lg font-bold text-gray-900 mb-6">Data Mahasiswa</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="npm" class="block text-sm font-medium text-gray-700 mb-2">NPM <span class="text-red-500">*</span></label>
                        <input type="text" name="npm" id="npm" value="{{ old('npm', $user->profile->npm ?? '') }}" :required="role === 'student'" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                        @error('npm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="class_name" class="block text-sm font-medium text-gray-700 mb-2">Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="class_name" id="class_name" value="{{ old('class_name', $user->profile->class_name ?? '') }}" :required="role === 'student'" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="Contoh: 3KA15">
                        @error('class_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                     <div>
                        <label for="phone_number_student" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" name="phone_number" id="phone_number_student" value="{{ old('phone_number', $user->profile->phone_number ?? '') }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                         @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>


            {{-- TOMBOL AKSI --}}
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-8">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">Perbarui Pengguna</button>
            </div>
        </form>
    </div>
@endsection
