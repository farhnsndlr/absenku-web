@extends($dashboardView)

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="max-w-4xl mx-auto">

        {{-- Flash Messages --}}
        @if (session('status') === 'profile-updated')
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200 shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">Sukses!</span>&nbsp;Profil Anda berhasil diperbarui.
                </div>
            </div>
        @elseif (session('status') === 'password-updated')
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200 shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">Sukses!</span>&nbsp;Password Anda berhasil diubah.
                </div>
            </div>
        @endif

        {{-- Kartu Profil Utama --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="md:flex">
                {{-- Kolom Kiri: Foto dan Info Singkat --}}
                <div class="md:w-1/3 bg-gray-50/50 p-8 flex flex-col items-center text-center border-b md:border-b-0 md:border-r border-gray-100">
                    <div class="relative mb-4">
                        @if($user->profile_photo_url)
                            <img class="w-32 h-32 rounded-full object-cover shadow-sm border-4 border-white"
                                 src="{{ $user->profile_photo_url }}?v={{ time() }}"
                                 alt="{{ $user->name }}">
                        @else
                            <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-sm border-4 border-white">
                                <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>

                    {{-- Badge Role dengan Warna Dinamis --}}
                    @php
                        $roleColors = [
                            'admin' => 'bg-red-100 text-red-800 border-red-200',
                            'lecturer' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'student' => 'bg-blue-100 text-blue-800 border-blue-200',
                        ];
                        $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                    @endphp
                    <span class="inline-block px-3 py-0.5 text-sm font-medium rounded-full mb-4 border {{ $roleColor }}">
                        {{ ucfirst($user->role) }}
                    </span>

                    @if($user->profile)
                        @if($user->profile instanceof \App\Models\LecturerProfile)
                            <div class="mt-2 p-3 bg-white rounded-lg border border-gray-100 w-full">
                                <p class="text-xs text-gray-500 uppercase font-semibold">Nomor Induk Dosen (NID)</p>
                                <p class="text-base font-bold text-gray-900">{{ $user->profile->nid }}</p>
                            </div>
                        @elseif($user->profile instanceof \App\Models\StudentProfile)
                             <div class="mt-2 p-3 bg-white rounded-lg border border-gray-100 w-full">
                                <p class="text-xs text-gray-500 uppercase font-semibold">Nomor Pokok Mahasiswa (NPM)</p>
                                <p class="text-base font-bold text-gray-900">{{ $user->profile->npm }}</p>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Kolom Kanan: Detail Informasi Akun --}}
                <div class="md:w-2/3 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Informasi Akun</h3>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            Edit Profil
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Telepon</label>
                             @if($user->profile && $user->profile->phone_number)
                                <p class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2">{{ $user->profile->phone_number }}</p>
                            @else
                                <p class="text-gray-400 italic text-base border-b border-gray-100 pb-2">Belum diatur</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Bergabung Sejak</label>
                            <p class="text-gray-900 text-base border-b border-gray-100 pb-2">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                        </div>

                        {{-- TAMBAHAN: Tampilkan Kelas untuk Mahasiswa --}}
                        @if($user->role === 'student' && isset($additionalData['student_classes']) && $additionalData['student_classes']->count() > 0)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Kelas</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($additionalData['student_classes'] as $className)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $className }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Keamanan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Keamanan</h3>
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl bg-gray-50/50">
                <div class="flex items-center">
                     <div class="w-10 h-10 bg-gray-200 text-gray-600 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Password</h4>
                        <p class="text-sm text-gray-500">Disarankan untuk memperbarui password Anda secara berkala untuk keamanan.</p>
                    </div>
                </div>
                <a href="{{ route('profile.password') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Ubah Password
                </a>
            </div>
        </div>
    </div>
@endsection
