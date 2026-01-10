@extends('lecturer.dashboard')

@section('title', 'Edit Sesi Kelas')
@section('page-title', 'Edit Jadwal Sesi Absensi')

@section('content')

@if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8"
         x-data="{
            sessionType: '{{ old('learning_type', $session->learning_type ?? $session->session_type) }}'
         }">

        <form action="{{ route('lecturer.sessions.update', $session->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Mata Kuliah (Readonly - Tidak Bisa Diubah) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah</label>
                <input type="text" readonly disabled
                       value="{{ $session->course->course_name }} ({{ $session->course->course_code }})"
                       class="w-full rounded-lg bg-gray-100 border-gray-300 cursor-not-allowed shadow-sm py-2.5 px-4 text-gray-600">
                <p class="text-xs text-gray-500 mt-1">Mata kuliah tidak dapat diubah.</p>
            </div>

            <div>
                <label for="class_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="class_name" id="class_name"
                       value="{{ old('class_name', $session->class_name) }}" required
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4"
                       placeholder="Contoh: 3KA15">
                @error('class_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="session_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kelas <span class="text-red-500">*</span></label>
                <input type="date" name="session_date" id="session_date"
                       value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}" required
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                @error('session_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time"
                           value="{{ old('start_time', $session->start_time->format('H:i')) }}" required
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time"
                           value="{{ old('end_time', $session->end_time->format('H:i')) }}" required
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Tipe Sesi (Online/Offline) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Sesi <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-6 mt-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="learning_type" value="offline" x-model="sessionType"
                               class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Offline (Tatap Muka)</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="learning_type" value="online" x-model="sessionType"
                               class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Online (Daring)</span>
                    </label>
                </div>
                @error('learning_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Lokasi (Hanya muncul jika Offline) --}}
            <div x-show="sessionType === 'offline'" x-cloak class="border-t border-gray-100 pt-6 transition-all">
                 <h3 class="text-lg font-bold text-gray-900 mb-4">Lokasi & Topik</h3>

                 <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Kampus <span class="text-red-500">*</span></label>

                    <select name="location_id" id="location_id"
                            :required="sessionType === 'offline'"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                        <option value="">-- Pilih Lokasi Kampus --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ old('location_id', $session->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Topik Pembahasan --}}
            <div>
                <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">Topik Pembahasan (Opsional)</label>
                <input type="text" name="topic" id="topic"
                       value="{{ old('topic', $session->topic) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4"
                       placeholder="Contoh: Pertemuan 6 : Clustering dan Klasifikasi">
                @error('topic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tambahan Input Status Sesi (Penting untuk Edit) --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Sesi <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    <option value="scheduled" {{ old('status', $session->status) == 'scheduled' ? 'selected' : '' }}>Terjadwal (Scheduled)</option>
                    <option value="open" {{ old('status', $session->status) == 'open' ? 'selected' : '' }}>Dibuka (Open)</option>
                    <option value="closed" {{ old('status', $session->status) == 'closed' ? 'selected' : '' }}>Selesai (Closed)</option>
                </select>
                 @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

<div>
    <label for="late_tolerance_minutes" class="block text-sm font-medium text-gray-700 mb-2">
        Batas Toleransi Terlambat (menit) <span class="text-red-500">*</span>
    </label>
    <input type="number" name="late_tolerance_minutes" id="late_tolerance_minutes"
           value="{{ old('late_tolerance_minutes', $session->late_tolerance_minutes) }}" required
           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
</div>


            {{-- Tombol Submit --}}
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-8">
                <a href="{{ route('lecturer.sessions.show', $session->id) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                   Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
