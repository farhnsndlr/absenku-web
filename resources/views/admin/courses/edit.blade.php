@extends('admin.dashboard')

@section('title', 'Edit Mata Kuliah')
@section('page-title', 'Edit Data Mata Kuliah')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        {{-- Form mengarah ke route update dengan ID course --}}
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT') {{-- PENTING: Method spoofing untuk update --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kode Mata Kuliah --}}
                <div>
                    <label for="course_code" class="block text-sm font-medium text-gray-700 mb-2">Kode MK <span class="text-red-500">*</span></label>
                    {{-- Menggunakan old() dengan default value dari database ($course->course_code) --}}
                    <input type="text" name="course_code" id="course_code" value="{{ old('course_code', $course->course_code) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('course_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Nama Mata Kuliah --}}
                <div>
                    <label for="course_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Mata Kuliah <span class="text-red-500">*</span></label>
                    <input type="text" name="course_name" id="course_name" value="{{ old('course_name', $course->course_name) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('course_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Waktu Mulai --}}
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai <span class="text-red-500">*</span></label>
                    {{-- PENTING: Format data datetime dari database ke format jam-menit (H:i) agar terbaca input time --}}
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($course->start_time)->format('H:i')) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                {{-- Waktu Selesai --}}
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($course->end_time)->format('H:i')) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Dosen Pengampu --}}
            <div>
                <label for="lecturer_id" class="block text-sm font-medium text-gray-700 mb-2">Dosen Pengampu <span class="text-red-500">*</span></label>
                <select name="lecturer_id" id="lecturer_id" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4">
                    <option value="">-- Pilih Dosen --</option>
                    @foreach($lecturers as $lecturer)
                        {{-- Logika untuk menandai dosen yang sedang terpilih --}}
                        <option value="{{ $lecturer->id }}" {{ old('lecturer_id', $course->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                            {{ $lecturer->name }}
                            {{-- Menampilkan NID jika ada --}}
                            {{ $lecturer->profile && $lecturer->profile->nid ? '(' . $lecturer->profile->nid . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('lecturer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-8">
                <a href="{{ route('admin.courses.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">Perbarui Mata Kuliah</button>
            </div>
        </form>
    </div>
@endsection
