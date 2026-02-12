@extends('admin.dashboard')

@section('title', 'Tambah Lokasi Kampus')
@section('page-title', 'Tambah Lokasi Geofence Baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 450px;
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">

        <a href="{{ route('admin.locations.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition mb-6 block">
            &larr; Kembali ke Daftar Lokasi
        </a>

        <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-6">
            @csrf

            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">Informasi Dasar</h3>

            {{-- Nama Lokasi --}}
            <div>
                <label for="location_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi (Gedung/Ruang) <span class="text-red-500">*</span></label>
                <input type="text" name="location_name" id="location_name" value="{{ old('location_name') }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="Contoh: Kampus Utama - Area Depan Gedung Rektorat">
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

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <button type="button" id="use-current-location"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.866-4 6-4 6s-4-2.134-4-6a8 8 0 1116 0c0 3.866-4 6-4 6s-4-2.134-4-6z"/>
                        <circle cx="12" cy="11" r="3" stroke-width="2"/>
                    </svg>
                    Gunakan Lokasi Saat Ini
                </button>
                <p class="text-xs text-gray-500">
                    Klik untuk mengambil koordinat GPS perangkat lalu sesuaikan pin di peta jika perlu.
                </p>
            </div>
            <p class="text-xs text-gray-500 mb-1" id="current-location-status"></p>
            <p class="text-xs text-gray-500 mb-4">Tips: Anda juga bisa klik langsung pada peta untuk mengatur titik lokasi secara manual.</p>

            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4 pt-4">Konfigurasi Geofence</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Latitude --}}
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', '-6.2088') }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="-6.256101" readonly>
                    @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Koordinat ini akan terisi otomatis dari peta.</p>
                </div>

                {{-- Longitude --}}
                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', '106.8456') }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="106.82311" readonly>
                    @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Koordinat ini akan terisi otomatis dari peta.</p>
                </div>

                {{-- Radius (Toleransi) --}}
                <div>
                    <label for="radius_meters" class="block text-sm font-medium text-gray-700 mb-2">Radius Toleransi (Meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="radius_meters" id="radius_meters" value="{{ old('radius_meters', '50') }}" required min="10" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4" placeholder="Contoh: 50">
                    @error('radius_meters') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Jarak maksimum dari titik pusat.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-8">
                <a href="{{ route('admin.locations.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">Simpan Lokasi</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof L === 'undefined') {
            console.error("Leaflet failed to load.");
            return;
        }

        // Koordinat default (Jakarta atau lokasi kampus)
        const initialLat = parseFloat("{{ old('latitude', '-6.354403319124305') }}");
        const initialLon = parseFloat("{{ old('longitude', '106.84160449325263') }}");
        const initialRadius = parseFloat(document.getElementById('radius_meters').value) || 50;

        // Inisialisasi Peta
        var map = L.map('map').setView([initialLat, initialLon], 15);

        // Lapisan peta (peta dasar) dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Marker yang bisa dipindah-pindah
        var marker = L.marker([initialLat, initialLon], {
            draggable: true,
            title: 'Drag untuk memindahkan lokasi'
        }).addTo(map);

        // Lingkaran untuk menampilkan radius
        var circle = L.circle([initialLat, initialLon], {
            radius: initialRadius,
            color: '#3b82f6',
            fillColor: '#3b82f6',
            fillOpacity: 0.2
        }).addTo(map);

        function syncLocation(lat, lng, setView = false) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            if (setView) {
                map.setView([lat, lng], 17);
            }
        }

        // Update input latitude dan longitude saat marker dipindah
        marker.on('dragend', function(e) {
            var latlng = marker.getLatLng();
            syncLocation(latlng.lat, latlng.lng);
        });

        // Klik peta untuk set titik lokasi manual
        map.on('click', function(e) {
            syncLocation(e.latlng.lat, e.latlng.lng, false);
            if (statusEl) {
                statusEl.textContent = `Lokasi diset manual: ${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
            }
        });

        // Update lingkaran radius saat input radius_meters berubah
        document.getElementById('radius_meters').addEventListener('input', function() {
            var newRadius = parseFloat(this.value);
            if (!isNaN(newRadius) && newRadius >= 10) {
                circle.setRadius(newRadius);
            }
        });

        // Set nilai awal untuk input
        document.getElementById('latitude').value = initialLat.toFixed(6);
        document.getElementById('longitude').value = initialLon.toFixed(6);

        const useCurrentBtn = document.getElementById('use-current-location');
        const statusEl = document.getElementById('current-location-status');
        if (useCurrentBtn) {
            useCurrentBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Perangkat tidak mendukung geolokasi.');
                    return;
                }

                useCurrentBtn.disabled = true;
                const originalText = useCurrentBtn.innerHTML;
                useCurrentBtn.innerHTML = 'Mengambil lokasi...';
                if (statusEl) {
                    statusEl.textContent = 'Mengambil lokasi GPS...';
                }

                let watchId = null;
                const stopWatch = () => {
                    if (watchId !== null) {
                        navigator.geolocation.clearWatch(watchId);
                        watchId = null;
                    }
                };

                const onSuccess = function(pos) {
                    const accuracy = Math.round(pos.coords.accuracy || 0);
                    syncLocation(pos.coords.latitude, pos.coords.longitude, true);
                    if (statusEl) {
                        statusEl.textContent = `Lokasi ditemukan (akurasi ±${accuracy}m).`;
                    }

                    if (accuracy <= 50) {
                        stopWatch();
                        useCurrentBtn.disabled = false;
                        useCurrentBtn.innerHTML = originalText;
                    }
                };

                const onError = function() {
                    stopWatch();
                    alert('Gagal mengambil lokasi. Pastikan izin GPS aktif.');
                    if (statusEl) {
                        statusEl.textContent = 'Gagal mengambil lokasi. Periksa izin GPS.';
                    }
                    useCurrentBtn.disabled = false;
                    useCurrentBtn.innerHTML = originalText;
                };

                navigator.geolocation.getCurrentPosition(onSuccess, onError, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                });

                watchId = navigator.geolocation.watchPosition(onSuccess, onError, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                });
            });
        }

        // Invalidate size setelah render untuk memastikan peta tampil dengan benar
        setTimeout(function() {
            map.invalidateSize();
        }, 100);
    });
</script>
@endpush
