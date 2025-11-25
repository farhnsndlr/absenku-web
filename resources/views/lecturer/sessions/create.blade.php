@extends('lecturer.dashboard')

@section('title', 'Buat Sesi Kelas')
@section('page-title', 'Jadwalkan Sesi Absensi Baru')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8"
         x-data="{
            selectedCourseId: '{{ old('course_id') }}',
            sessionType: '{{ old('session_type', 'offline') }}',
            // Data default mata kuliah dari controller, di-encode ke JSON
            coursesDefaults: {{ json_encode($coursesDefaults) }},

            // Fungsi yang dipanggil saat dropdown course berubah
            updateDefaults() {
                const defaults = this.coursesDefaults[this.selectedCourseId];
                if (defaults) {
                    // Isi field dengan data default jika belum ada inputan lama (old)
                    if (!'{{ old('start_time') }}') document.getElementById('start_time').value = defaults.start_time;
                    if (!'{{ old('end_time') }}') document.getElementById('end_time').value = defaults.end_time;
                    if (!'{{ old('session_type') }}') this.sessionType = defaults.session_type;
                    if (!'{{ old('location_id') }}') document.getElementById('location_id').value = defaults.location_id;
                }
            }
         }"
         {{-- Panggil fungsi updateDefaults saat inisialisasi jika ada course yang terpilih (misal setelah error validasi) --}}
         x-init="if(selectedCourseId) updateDefaults()">

        <form action="{{ route('lecturer.sessions.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Pilihan Mata Kuliah --}}
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah <span class="text-red-500">*</span></label>
                {{-- x-model ke selectedCourseId dan panggil updateDefaults saat berubah --}}
                <select name="course_id" id="course_id" x-model="selectedCourseId" @change="updateDefaults()" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    @foreach($myCourses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->course_name }} ({{ $course->course_code }})
                        </option>
                    @endforeach
                </select>
                @error('course_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1" x-show="!selectedCourseId">Pilih mata kuliah untuk mengisi jadwal default otomatis.</p>
            </div>

            {{--  Nama Kelas --}}
            <div>
                <label for="class_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas <span class="text-red-500">*</span></label>
                {{-- Perhatikan name="class_name" dan id="class_name" --}}
                <input type="text" name="class_name" id="class_name"
                       value="{{ old('class_name') }}" required
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4"
                       placeholder="Contoh: 3KA15">
                <p class="text-xs text-gray-500 mt-1">Masukkan nama kelas untuk sesi ini agar mudah dibedakan.</p>
                @error('class_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal Sesi --}}
            <div>
                <label for="session_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kelas <span class="text-red-500">*</span></label>
                {{-- Default tanggal hari ini --}}
                <input type="date" name="session_date" id="session_date" value="{{ old('session_date', date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                @error('session_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Jam Mulai --}}
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Jam Selesai --}}
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Tipe Sesi (Online/Offline) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Sesi <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-6 mt-2">
                    <label class="flex items-center cursor-pointer">
                        {{-- x-model ke sessionType --}}
                        <input type="radio" name="session_type" value="offline" x-model="sessionType" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Offline (Tatap Muka)</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        {{-- x-model ke sessionType --}}
                        <input type="radio" name="session_type" value="online" x-model="sessionType" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Online (Daring)</span>
                    </label>
                </div>
                @error('session_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pilihan Lokasi (Hanya muncul jika Offline) --}}
            <div x-show="sessionType === 'offline'" x-cloak class="border-t border-gray-100 pt-6 transition-all">
                 <h3 class="text-lg font-bold text-gray-900 mb-4">Lokasi & Topik</h3>
                 <div class="mb-4">
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi Kampus <span class="text-red-500">*</span></label>
                    {{-- Required jika sessionType offline --}}
                    <select name="location_id" id="location_id" :required="sessionType === 'offline'" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                        <option value="">-- Pilih Gedung / Ruangan --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Topik Pembahasan (Opsional) --}}
            <div>
                <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">Topik Pembahasan (Opsional)</label>
                <input type="text" name="topic" id="topic" value="{{ old('topic') }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="Contoh: Pertemuan 6 : Clustering dan Klasifikasi">
                @error('topic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-8">
                <a href="{{ route('lecturer.sessions.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">Jadwalkan Sesi</button>
            </div>
        </form>
    </div>
@endsection
