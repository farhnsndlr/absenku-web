@extends($dashboardView)

@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')

@if($dashboardView === 'layouts.dashboard')
@section('navigation')
    @include('student.partials.navigation')
@endsection
@endif

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Perbarui Password</h2>
        <p class="text-sm text-gray-500 mb-6">Pastikan password Anda aman dan tidak mudah ditebak.</p>

                <form method="post" action="{{ route('profile.password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            {{-- Password Saat Ini --}}
            @if(!empty(auth()->user()->password))
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                    {{-- Wrapper Alpine.js untuk scope ini saja --}}
                    <div class="relative" x-data="{ show: false }">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="current_password"
                            id="current_password"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4 pr-12" {{-- Tambahkan pr-12 agar teks tidak ketimpa ikon --}}
                        >
                        {{-- Tombol Toggle Icon --}}
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                            {{-- Ikon Mata Terbuka (Muncul saat hidden / !show) --}}
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{-- Ikon Mata Dicoret (Muncul saat visible / show) --}}
                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('current_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @else
                <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    Akun Google Anda belum memiliki password. Silakan buat password baru di bawah ini.
                </div>
            @endif

            {{-- Password Baru --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                {{-- Wrapper Alpine.js Scope Baru --}}
                <div class="relative" x-data="{ show: false }">
                    <input
                        :type="show ? 'text' : 'password'"
                        name="password"
                        id="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4 pr-12"
                    >
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                         <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                {{-- Wrapper Alpine.js Scope Baru --}}
                <div class="relative" x-data="{ show: false }">
                    <input
                        :type="show ? 'text' : 'password'"
                        name="password_confirmation"
                        id="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4 pr-12"
                    >
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-gray-700 focus:outline-none">
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

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">Simpan Password</button>
                <a href="{{ route('profile.show') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
