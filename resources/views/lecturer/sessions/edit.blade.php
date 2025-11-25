@extends('lecturer.dashboard')

@section('title', 'Edit Sesi Kelas')
@section('page-title', 'Edit Jadwal Sesi Absensi')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8"
         x-data="{
            sessionType: '{{ old('session_type', $session->learning_type) }}'
         }">

        <form action="{{ route('lecturer.sessions.update', $session->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Mata Kuliah (Tidak Bisa Diubah) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah</label>
                <input type="text" disabled value="{{ $session->course->course_name }} ({{ $session->course->course_code }})"
                       class="w-full rounded-lg bg-gray-100 border-gray-300 shadow-sm py-2.5 px-4">
            </div>

            {{-- Tanggal Sesi --}}
            <div>
                <label for="session_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kelas</label>
                <input type="date" name="session_date" id="session_date"
                       value="{{ old('session_date', $session->session_date) }}" required
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                @error('session_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jam Mulai & Selesai --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                    <input type="time" name="start_time" id="start_time"
                           value="{{ old('start_time', $session->start_time) }}" required
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                    <input type="time" name="end_time" id="end_time"
                           value="{{ old('end_time', $session->end_time) }}" required
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Tipe Sesi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Sesi</label>
                <div class="flex items-center gap-6 mt-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="session_type" value="offline" x-model="sessionType"
                               class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Offline</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="session_type" value="online" x-model="sessionType"
                               class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-gray-900">Online</span>
                    </label>
                </div>
                @error('session_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Lokasi (Offline Only) --}}
            <div x-show="sessionType === 'offline'" x-cloak class="border-t border-gray-100 pt-6 transition-all">
                 <h3 class="text-lg font-bold text-gray-900 mb-4">Lokasi & Topik</h3>

                 <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Kampus</label>
                    <select name="location_id" id="location_id"
                            :required="sessionType === 'offline'"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
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
                <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">Topik Pembahasan</label>
                <input type="text" name="topic" id="topic"
                       value="{{ old('topic', $session->description) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4"
                       placeholder="Contoh: Pertemuan 6 : Clustering dan Klasifikasi">
                @error('topic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
