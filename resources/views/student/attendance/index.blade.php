@extends('layouts.dashboard')

@section('title', 'Absensi Saya')

@section('content')
<div class="space-y-6">

    <h1 class="text-2xl font-bold text-gray-800">Daftar Sesi Absensi Hari Ini</h1>

    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded-md">{{ session('error') }}</div>
    @endif

    @if($activeSessions->isEmpty())
        <div class="p-6 bg-gray-50 text-center rounded-lg border">
            <p class="text-gray-600">Tidak ada sesi absensi hari ini.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeSessions as $session)
                <div class="p-5 border rounded-xl shadow-sm bg-white space-y-3">

                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold">{{ $session->course->name }}</h2>

                        @if($session->is_finished)
                            <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">Selesai</span>
                        @elseif($session->is_ongoing)
                            <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-600">Sedang Berlangsung</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-600">Belum Mulai</span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-500 font-medium">{{ $session->class_name }}</p>

                    <div class="text-sm text-gray-600">
                        <p>Dosen: <span class="font-medium">{{ $session->course->lecturer->name }}</span></p>
                        <p>Jenis: <span class="font-medium">{{ ucfirst($session->learning_type) }}</span></p>

                        @if($session->location && $session->learning_type === 'offline')
                            <p>Lokasi: <span class="font-medium">{{ $session->location->name }}</span></p>
                        @endif

                        <p>Waktu:
                            <span class="font-medium">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                            </span>
                        </p>
                    </div>

                    {{-- STATUS BUTTON --}}
                    @if($session->has_checked_in)
                        <button class="w-full bg-gray-200 text-gray-600 py-2 rounded-md cursor-not-allowed">
                            Sudah Absen
                        </button>

                    @elseif($session->is_ongoing)
                        {{-- FORM ABSEN --}}
                        <form action="{{ route('student.attendance.store', $session->id) }}"
                              method="POST"
                              class="space-y-3">

                            @csrf

                            {{-- Foto --}}
                            <input type="file"
                                   accept="image/*"
                                   capture="camera"
                                   id="photoInput-{{ $session->id }}"
                                   class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2">

                            <input type="hidden" name="photo_base64" id="photoBase64-{{ $session->id }}">

                            {{-- Geo Location --}}
                            <input type="hidden" name="latitude" id="latitudeField-{{ $session->id }}">
                            <input type="hidden" name="longitude" id="longitudeField-{{ $session->id }}">

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md">
                                Absen Sekarang
                            </button>
                        </form>

                        {{-- FOTO TO BASE64 --}}
                        <script>
                            document.getElementById("photoInput-{{ $session->id }}")
                                .addEventListener("change", function (event) {
                                    const file = event.target.files[0];
                                    const reader = new FileReader();

                                    reader.onload = function () {
                                        document.getElementById("photoBase64-{{ $session->id }}").value = reader.result;
                                    };

                                    if (file) reader.readAsDataURL(file);
                                });
                        </script>

                    @else
                        <button class="w-full bg-gray-300 text-gray-500 py-2 rounded-md cursor-not-allowed">
                            Belum Bisa Absen
                        </button>
                    @endif

                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- GET LOCATION --}}
<script>
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.querySelectorAll('[id^="latitudeField"]').forEach(el => {
                el.value = pos.coords.latitude;
            });
            document.querySelectorAll('[id^="longitudeField"]').forEach(el => {
                el.value = pos.coords.longitude;
            });
        });
    }
</script>

@endsection
