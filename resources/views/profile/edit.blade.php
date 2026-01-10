@extends($dashboardView)

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@if($dashboardView === 'layouts.dashboard')
@section('navigation')
    @include('student.partials.navigation')
@endsection
@endif

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Pribadi</h2>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                <input type="file" id="photo" name="photo" class="hidden"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <label class="block text-sm font-medium text-gray-700 mb-2" for="photo">
                    Foto Profil
                </label>

                <div class="flex items-center gap-4">
                    <div class="mt-2" x-show="! photoPreview">
                        @if($user->profile_photo_url)
                             <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-full h-20 w-20 object-cover">
                        @else
                            {{-- Tampilan Inisial Default --}}
                            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-2xl font-bold text-blue-600">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-2" x-show="photoPreview" style="display: none;">
                        <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            x-on:click.prevent="$refs.photo.click()">
                        Pilih Foto Baru
                    </button>
                </div>
                @error('photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>


            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->profile->phone_number ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4">
                @error('phone_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

             {{-- Field Spesifik Berdasarkan Role --}}
            @if($user->role === 'lecturer')
                <div>
                    <label for="nid" class="block text-sm font-medium text-gray-700 mb-1">NID (Nomor Induk Dosen)</label>
                    <input type="text" name="nid" id="nid" value="{{ old('nid', $user->profile->nid ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4">
                    @error('nid') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @elseif($user->role === 'student')
                 <div>
                    <label for="npm" class="block text-sm font-medium text-gray-700 mb-1">NPM (Nomor Pokok Mahasiswa)</label>
                    <input type="text" name="npm" id="npm" value="{{ old('npm', $user->profile->npm ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4">
                    @error('npm') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="class_name" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <input type="text" name="class_name" id="class_name" value="{{ old('class_name', $user->profile->class_name ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4" placeholder="Contoh: 3KA15">
                    @error('class_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endif


            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">Simpan Perubahan</button>
                <a href="{{ route('profile.show') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
