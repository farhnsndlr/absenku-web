@extends('lecturer.dashboard')
@section('title', 'Buat Sesi Kelas')
@section('page-title', 'Jadwalkan Sesi Absensi Baru')

@section('content')
<style>
    .input-stretch[type="date"],
    .input-stretch[type="time"] {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box;
    }
</style>
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8"
    x-data="{
       selectedCourseId: '{{ old('course_id') }}',
       sessionType: '{{ old('learning_type', 'offline') }}',
       // Data default mata kuliah dari controller
       coursesDefaults: {{ json_encode($coursesDefaults) }},
       updateDefaults() {
           const defaults = this.coursesDefaults[this.selectedCourseId];
           if (defaults) {
               // Hanya isi jika tidak ada input lama (old) agar tidak menimpa input user saat validasi gagal
               if (!'{{ old('start_time') }}') document.getElementById('start_time').value = defaults.start_time;
               if (!'{{ old('end_time') }}') document.getElementById('end_time').value = defaults.end_time;
               if (!'{{ old('learning_type') }}') this.sessionType = defaults.learning_type;
               // Pastikan elemen location_id ada sebelum set value (karena bisa tersembunyi)
               const locEl = document.getElementById('location_id');
               if (locEl && !'{{ old('location_id') }}') locEl.value = defaults.location_id;
           }
       }
    }"
    {{-- Inisialisasi ulang defaults jika terjadi error validasi dan course sudah terpilih --}}
    x-init="if(selectedCourseId) updateDefaults()">

    <form action="{{ route('lecturer.sessions.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- --- Bagian 1: Informasi Utama Sesi --- --}}
        <div class="space-y-6 border-b border-gray-100 pb-6">
            {{-- Pilihan Mata Kuliah --}}
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah <span class="text-red-500">*</span></label>
                <select name="course_id" id="course_id" x-model="selectedCourseId" @change="updateDefaults()" required
                    class="w-full rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('course_id') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    @foreach($myCourses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->course_name }} ({{ $course->course_code }})
                        </option>
                    @endforeach
                </select>
                @error('course_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                {{-- Helper text dinamis menggunakan Alpine --}}
                <p class="text-xs text-gray-500 mt-1" x-show="!selectedCourseId">Jam dan tipe sesi akan terisi otomatis berdasarkan pengaturan mata kuliah.</p>
            </div>

            {{-- Nama Kelas --}}
            <div>
                <label for="class_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="class_name" id="class_name" value="{{ old('class_name') }}" required
                    class="w-full rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('class_name') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}"
                    placeholder="Contoh: 3KA15">
                <p class="text-xs text-gray-500 mt-1">Masukkan identitas kelas atau untuk sesi ini.</p>
                @error('class_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>


        {{-- --- Bagian 2: Waktu Pelaksanaan --- --}}
        <div class="space-y-6 border-b border-gray-100 pb-6 pt-2">
             <h3 class="text-lg font-bold text-gray-900">Waktu Pelaksanaan</h3>
            {{-- Tanggal Sesi --}}
            <div>
                <label for="session_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="session_date" id="session_date" value="{{ old('session_date', date('Y-m-d')) }}" required
                    class="input-stretch block w-full appearance-none rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('session_date') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}">
                @error('session_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Jam Mulai --}}
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                        class="input-stretch block w-full appearance-none rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('start_time') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}">
                    @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Jam Selesai --}}
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required
                        class="input-stretch block w-full appearance-none rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('end_time') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}">
                    @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

             {{-- Batas Toleransi --}}
            <div>
                <label for="late_tolerance_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                    Batas Toleransi Terlambat (menit) <span class="text-red-500">*</span>
                </label>
                {{-- Tambahkan min="0" --}}
                <input type="number" min="0" name="late_tolerance_minutes" id="late_tolerance_minutes"
                    value="{{ old('late_tolerance_minutes', 10) }}" required
                    class="w-full rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('late_tolerance_minutes') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}">
                <p class="text-xs text-gray-500 mt-1">Mahasiswa dianggap terlambat setelah melewati batas waktu ini dari jam mulai.</p>
                @error('late_tolerance_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>


        {{-- --- Bagian 3: Detail & Lokasi --- --}}
        <div class="space-y-6 pt-2">
             <h3 class="text-lg font-bold text-gray-900">Detail Sesi</h3>

            {{-- Topik Pembahasan (Dipindah ke luar blok offline, karena online juga butuh topik) --}}
            <div>
                <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">Topik Pembahasan (Opsional)</label>
                <input type="text" name="topic" id="topic" value="{{ old('topic') }}"
                    class="w-full rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('topic') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}"
                    placeholder="Contoh: Pertemuan 6 : Clustering dan Klasifikasi">
                @error('topic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tipe Sesi (Online/Offline) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Sesi <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-6 mt-2 p-1">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="learning_type" value="offline" x-model="sessionType" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Offline (Tatap Muka)</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="learning_type" value="online" x-model="sessionType" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Online (Daring)</span>
                    </label>
                </div>
                @error('learning_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pilihan Lokasi (Hanya muncul jika Offline) --}}
            {{-- Gunakan x-cloak untuk mencegah konten berkedip saat loading --}}
            <div x-show="sessionType === 'offline'" x-cloak class="transition-all ease-in-out duration-300">
                 <div class="mb-4">
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Kampus / Ruangan <span class="text-red-500">*</span></label>
                    {{-- Binding :required agar HTML5 validation hanya aktif jika offline --}}
                    <select name="location_id" id="location_id" :required="sessionType === 'offline'"
                        class="w-full rounded-lg border bg-white shadow-sm py-2.5 px-4 {{ $errors->has('location_id') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }}">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                     <p class="text-xs text-gray-500 mt-1">Wajib diisi untuk sesi tatap muka.</p>
                </div>
            </div>
        </div>


        {{-- Tombol Submit --}}
        <div class="flex justify-end gap-3 border-t border-gray-100 pt-8 bg-gray-50 -mx-8 -mb-8 p-8 rounded-b-xl mt-8">
            <a href="{{ route('lecturer.sessions.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium shadow-sm">Batal</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-md hover:shadow-lg">
                Buat Jadwal Sesi
            </button>
        </div>
    </form>
</div>

@endsection
