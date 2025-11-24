@extends('admin.dashboard')

@section('title', 'Edit Lokasi Kampus')
@section('page-title', 'Edit Data Lokasi Geofence')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">

        <a href="{{ route('admin.locations.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition mb-6 block">
            &larr; Kembali ke Daftar Lokasi
        </a>

        <form action="{{ route('admin.locations.update', $location->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">Informasi Dasar</h3>

            {{-- Nama Lokasi --}}
            <div>
                <label for="location_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi (Gedung/Ruang) <span class="text-red-500">*</span></label>
                <input type="text" name="location_name" id="location_name" value="{{ old('location_name', $location->location_name) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="Contoh: Kampus Utama - Area Depan Gedung Rektorat">
                @error('location_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4 pt-4">Pilih Lokasi di Peta</h3>

            {{-- Instruksi Penggunaan Peta --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-blue-900 mb-2">📍 Cara Menggunakan Peta:</h4>
                        <ul class="text-sm text-blue-800 space-y-1.5">
                            <li class="flex items-start">
                                <span class="font-semibold mr-2 text-blue-600">1.</span>
                                <span><strong>Geser Peta:</strong> Klik dan drag peta untuk mencari lokasi kampus Anda</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold mr-2 text-blue-600">2.</span>
                                <span><strong>Zoom In/Out:</strong> Gunakan tombol <kbd class="px-1.5 py-0.5 bg-white border border-blue-300 rounded text-xs">+</kbd> / <kbd class="px-1.5 py-0.5 bg-white border border-blue-300 rounded text-xs">−</kbd> atau scroll mouse untuk memperbesar/memperkecil</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold mr-2 text-blue-600">3.</span>
                                <span><strong>Pindahkan Pin:</strong> Drag pin merah ke lokasi yang diinginkan (gedung/ruang kelas)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold mr-2 text-blue-600">4.</span>
                                <span><strong>Atur Radius:</strong> Sesuaikan radius toleransi di bawah untuk menentukan area absensi yang valid</span>
                            </li>
                        </ul>
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <p class="text-xs text-blue-700">
                                💡 <strong>Tips:</strong> Lingkaran biru menunjukkan area valid untuk absensi. Mahasiswa harus berada dalam lingkaran ini saat melakukan absensi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DIV UNTUK PETA --}}
            <div id="map"></div>

            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4 pt-4">Konfigurasi Geofence</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Latitude --}}
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $location->latitude) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="-6.256101" readonly>
                    @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Koordinat ini akan terisi otomatis dari peta.</p>
                </div>

                {{-- Longitude --}}
                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $location->longitude) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="106.82311" readonly>
                    @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Koordinat ini akan terisi otomatis dari peta.</p>
                </div>

                {{-- Radius (Toleransi) --}}
                <div>
                    <label for="radius_meters" class="block text-sm font-medium text-gray-700 mb-2">Radius Toleransi (Meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="radius_meters" id="radius_meters" value="{{ old('radius_meters', $location->radius_meters) }}" required min="10" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="Contoh: 50">
                    @error('radius_meters') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Jarak maksimum dari titik pusat.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-8">
                <a href="{{ route('admin.locations.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">Perbarui Lokasi</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof L === 'undefined') {
            console.error("Leaflet failed to load.");
            return;
        }

        // Ambil koordinat dari data lokasi yang ada
        const initialLat = parseFloat("{{ old('latitude', $location->latitude) }}");
        const initialLon = parseFloat("{{ old('longitude', $location->longitude) }}");
        const initialRadius = parseFloat("{{ old('radius_meters', $location->radius_meters) }}") || 50;

        // Inisialisasi Peta
        var map = L.map('map').setView([initialLat, initialLon], 17); // Zoom lebih dekat untuk edit

        // Tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Marker yang bisa dipindah-pindah
        var marker = L.marker([initialLat, initialLon], {
            draggable: true,
            title: 'Drag untuk memindahkan lokasi'
        }).addTo(map);

        // Lingkaran radius
        var circle = L.circle([initialLat, initialLon], {
            color: '#3b82f6',
            fillColor: '#3b82f6',
            fillOpacity: 0.2,
            radius: initialRadius
        }).addTo(map);

        // Update input latitude dan longitude saat marker dipindah
        marker.on('dragend', function(e) {
            var latlng = marker.getLatLng();
            document.getElementById('latitude').value = latlng.lat.toFixed(6);
            document.getElementById('longitude').value = latlng.lng.toFixed(6);
            circle.setLatLng(latlng); // Pindahkan lingkaran juga
        });

        // Update lingkaran radius saat input radius_meters berubah
        document.getElementById('radius_meters').addEventListener('input', function() {
            var newRadius = parseFloat(this.value);
            if (!isNaN(newRadius) && newRadius >= 10) {
                circle.setRadius(newRadius);
            }
        });

        // Invalidate size setelah render untuk memastikan peta tampil dengan benar
        setTimeout(function() {
            map.invalidateSize();
        }, 100);
    });
</script>
@endpush
