@extends($dashboardView)

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="max-w-4xl mx-auto">

        @if (session('status') === 'profile-updated')
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200" role="alert">
                <span class="font-medium">Sukses!</span> Profil Anda berhasil diperbarui.
            </div>
        @elseif (session('status') === 'password-updated')
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200" role="alert">
                <span class="font-medium">Sukses!</span> Password Anda berhasil diubah.
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="md:flex">
                <div class="md:w-1/3 bg-gray-50 p-8 flex flex-col items-center text-center border-b md:border-b-0 md:border-r border-gray-100">
                    @if($user->profile_photo_url)
                        {{-- Jika ada foto, tampilkan gambar dengan cache busting --}}
                        <img class="w-32 h-32 rounded-full object-cover mb-4 shadow-sm border-4 border-white"
                             src="{{ $user->profile_photo_url }}?v={{ time() }}"
                             alt="{{ $user->name }}">
                    @else
                        {{-- Jika tidak ada foto, tampilkan inisial (kode lama) --}}
                        <div class="w-32 h-32 bg-blue-100 rounded-full flex items-center justify-center mb-4 shadow-sm border-4 border-white">
                            <span class="text-4xl font-bold text-blue-600">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    @endif
                    <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                    <span class="inline-block px-3 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full mb-4">
                        {{ ucfirst($user->role) }}
                    </span>

                    @if($user->profile)
                        @if($user->profile instanceof \App\Models\LecturerProfile)
                            <p class="text-sm text-gray-500">NID: <span class="font-medium text-gray-700">{{ $user->profile->nid }}</span></p>
                        @elseif($user->profile instanceof \App\Models\StudentProfile)
                            <p class="text-sm text-gray-500">NPM: <span class="font-medium text-gray-700">{{ $user->profile->npm }}</span></p>
                        @endif
                    @endif
                </div>

                <div class="md:w-2/3 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Informasi Akun</h3>
                        <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Profil
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium">{{ $user->profile->full_name ?? $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Telepon</label>
                            <p class="text-gray-900 font-medium">{{ $user->profile->phone_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Bergabung Sejak</label>
                            <p class="text-gray-900">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($user->role === 'lecturer' && isset($additionalData['courses_taught']))
            {{-- Kartu Mata Kuliah Dosen --}}
             <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mata Kuliah yang Diampu</h3>
                @if($additionalData['courses_taught']->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($additionalData['courses_taught'] as $course)
                            <div class="p-4 border border-gray-200 rounded-lg flex items-start gap-4">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $course->course_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $course->course_code }} • {{ $course->credit_hours }} SKS</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Belum ada mata kuliah yang diampu.</p>
                @endif
            </div>
        @elseif($user->role === 'student' && isset($additionalData['courses_enrolled']))
            {{-- Kartu Mata Kuliah Mahasiswa --}}
             <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mata Kuliah yang Diambil</h3>
                @if($additionalData['courses_enrolled']->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                    <th class="pb-3">Kode</th>
                                    <th class="pb-3">Mata Kuliah</th>
                                    <th class="pb-3">SKS</th>
                                    <th class="pb-3">Dosen Pengampu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($additionalData['courses_enrolled'] as $course)
                                    <tr>
                                        <td class="py-3 text-sm text-gray-900 font-medium">{{ $course->course_code }}</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $course->course_name }}</td>
                                        <td class="py-3 text-sm text-gray-500">{{ $course->credit_hours }}</td>
                                        <td class="py-3 text-sm text-gray-500">
                                            {{ $course->lecturer->lecturerProfile->full_name ?? $course->lecturer->name ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Belum mengambil mata kuliah apapun.</p>
                @endif
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Keamanan</h3>
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div>
                    <h4 class="font-medium text-gray-900">Password</h4>
                    <p class="text-sm text-gray-500">Disarankan untuk memperbarui password secara berkala.</p>
                </div>
                <a href="{{ route('profile.password') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Ubah Password
                </a>
            </div>
        </div>
    </div>
@endsection
