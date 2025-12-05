@extends('layouts.student')

@section('content')
<h2>Form Presensi</h2>

<form action="{{ route('student.checkin.submit', $session->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Status --}}
    <label>Status:</label><br>
    <input type="radio" name="status" value="hadir" checked> Hadir<br>
    <input type="radio" name="status" value="izin"> Izin<br>
    <input type="radio" name="status" value="sakit"> Sakit<br>
    <br>

    {{-- Foto --}}
    <label>Bukti Foto:</label>
    <input type="file" name="photo" accept="image/*" capture="camera" required>
    <br><br>

    {{-- Jika onsite, tampilkan map --}}
    @if ($session->learning_type === 'offline')
    <div id="map" style="height:300px;"></div>

    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lng">

    <br>
    <span id="geo-status"></span>
    <br><br>
    @endif

    <button type="submit" id="submit-btn" class="btn btn-primary">Submit Presensi</button>
</form>

@endsection

@section('scripts')
@if ($session->learning_type === 'offline')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
navigator.geolocation.getCurrentPosition(success, error);

function success(pos) {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;

    document.getElementById("lat").value = lat;
    document.getElementById("lng").value = lng;

    // Init Map
    const map = L.map('map').setView([lat, lng], 18);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Marker mahasiswa
    L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Anda");

    // Marker lokasi kelas
    const classLat = {{ $location->latitude }};
    const classLng = {{ $location->longitude }};
    const radius = {{ $location->radius }};

    L.marker([classLat, classLng], {color: 'red'}).addTo(map).bindPopup("Lokasi Kelas");

    // Circle Geofence
    L.circle([classLat, classLng], {
        radius: radius,
        color: 'red',
        fillOpacity: 0.1
    }).addTo(map);

    // Hitung jarak
    const distance = getDistance(lat, lng, classLat, classLng);

    if (distance <= radius) {
        document.getElementById("geo-status").innerHTML = "<b style='color:green'>Anda berada dalam radius presensi</b>";
    } else {
        document.getElementById("geo-status").innerHTML = "<b style='color:red'>Anda berada di luar radius presensi</b>";
        document.getElementById("submit-btn").disabled = true;
    }
}

function error() {
    alert("Gagal mendapatkan lokasi. Aktifkan GPS!");
}

function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon/2) * Math.sin(dLon/2);

    return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
}

function deg2rad(deg) {
    return deg * (Math.PI/180);
}
</script>
@endif
@endsection
