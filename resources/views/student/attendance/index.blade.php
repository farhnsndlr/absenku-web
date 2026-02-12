@extends('layouts.dashboard')

@section('navigation')
    @include('student.partials.navigation')
@endsection

@section('title', 'Absensi Saya')

@section('content')
<div class="space-y-6">

    <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 via-white to-white px-6 py-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sesi Absensi Hari Ini</h1>
                <p class="text-sm text-gray-600">Pantau sesi aktif dan lakukan presensi tepat waktu.</p>
            </div>
           
        </div>
    </div>

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
        @php
            $groupedSessions = [
                [
                    'title' => 'Berlangsung',
                    'subtitle' => 'Sesi aktif dan yang akan dimulai hari ini.',
                    'tone' => 'blue',
                    'items' => $activeSessions->filter(function ($session) {
                        return $session->is_ongoing || $session->time_status === 'upcoming';
                    }),
                ],
                [
                    'title' => 'Terlewat',
                    'subtitle' => 'Sesi selesai tanpa presensi.',
                    'tone' => 'amber',
                    'items' => $activeSessions->filter(function ($session) {
                        return $session->is_finished && !$session->has_checked_in;
                    }),
                ],
                [
                    'title' => 'Selesai',
                    'subtitle' => 'Sesi yang sudah tercatat presensi.',
                    'tone' => 'emerald',
                    'items' => $activeSessions->filter(function ($session) {
                        return $session->is_finished && $session->has_checked_in;
                    }),
                ],
            ];
        @endphp

        @foreach($groupedSessions as $group)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $group['title'] }}</h2>
                        <p class="text-sm text-gray-500">{{ $group['subtitle'] }}</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold
                        {{ $group['tone'] === 'blue' ? 'bg-blue-100 text-blue-700' : ($group['tone'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                        <span class="h-2 w-2 rounded-full
                            {{ $group['tone'] === 'blue' ? 'bg-blue-600' : ($group['tone'] === 'amber' ? 'bg-amber-600' : 'bg-emerald-600') }}"></span>
                        {{ $group['items']->count() }} sesi
                    </span>
                </div>

                @if($group['items']->isEmpty())
                    <div class="mt-5 rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                        Belum ada sesi pada kategori ini.
                    </div>
                @else
                    <div class="mt-5 space-y-4">
                        @foreach($group['items'] as $session)
                            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white/70 px-5 py-4 shadow-[0_18px_40px_-30px_rgba(15,23,42,0.6)] backdrop-blur transition hover:border-slate-300">
                                <div class="absolute inset-y-0 left-0 w-1.5 bg-gradient-to-b from-slate-900 via-blue-600 to-emerald-500"></div>
                                <div class="absolute -right-12 -top-10 h-32 w-32 rounded-full bg-blue-100/40 blur-2xl"></div>

                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="flex min-w-[110px] flex-col items-center rounded-2xl border border-slate-200 bg-white px-3 py-2 text-center shadow-sm">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                {{ \Carbon\Carbon::parse($session->session_date)->translatedFormat('D') }}
                                            </span>
                                            <span class="text-lg font-bold text-slate-900">
                                                {{ \Carbon\Carbon::parse($session->session_date)->format('d') }}
                                            </span>
                                            <span class="text-xs font-medium text-slate-500">
                                                {{ \Carbon\Carbon::parse($session->session_date)->translatedFormat('M Y') }}
                                            </span>
                                        </div>

                                        <div class="space-y-2">
                                            <div class="flex items-center gap-3">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $session->course->name }}</h3>
                                                @if($session->is_finished)
                                                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">Selesai</span>
                                                @elseif($session->is_ongoing)
                                                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">Sedang Berlangsung</span>
                                                @else
                                                    <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-700">Belum Mulai</span>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-600">
                                                    {{ $session->class_name }}
                                                </span>
                                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-600">
                                                    Kode: {{ 'S-' . str_pad($session->id, 5, '0', STR_PAD_LEFT) }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide
                                                    {{ $session->learning_type === 'offline' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                                                    @if($session->learning_type === 'offline')
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.35 7-10a7 7 0 10-14 0c0 5.65 7 10 7 10z"/><circle cx="12" cy="11" r="3" stroke-width="2"/></svg>
                                                        Offline
                                                    @else
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 9a7.5 7.5 0 0115 0M7.5 12a4.5 4.5 0 019 0M10.5 15a1.5 1.5 0 013 0"/></svg>
                                                        Online
                                                    @endif
                                                </span>
                                                <span class="text-slate-400">|</span>
                                                <span class="text-slate-600">
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                </span>
                                            </div>

                                            <div class="text-sm text-slate-600 space-y-1">
                                                <p>Dosen: <span class="font-medium text-slate-900">{{ $session->course->lecturer->name }}</span></p>
                                                @if($session->learning_type === 'offline')
                                                    <p>Lokasi: <span class="font-medium text-slate-900">{{ $session->location?->location_name ?? 'Belum diatur' }}</span></p>
                                                @else
                                                    <p>Lokasi: <span class="font-medium text-slate-900">Daring</span></p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        @if($session->has_checked_in)
                                            <button class="w-full rounded-md bg-slate-100 py-2 text-slate-500 cursor-not-allowed sm:w-44">
                                                Sudah Absen
                                            </button>
                                        @elseif($session->is_ongoing)
                                            <button type="button"
                                                    class="w-full rounded-md bg-blue-600 py-2 text-white shadow-sm hover:bg-blue-700 sm:w-44"
                                                    data-attendance-modal-trigger="attendance-modal-{{ $session->id }}">
                                                Isi Presensi
                                            </button>
                                        @else
                                            <button class="w-full rounded-md bg-slate-200 py-2 text-slate-500 cursor-not-allowed sm:w-44">
                                                Belum Bisa Absen
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL --}}
                        <div id="attendance-modal-{{ $session->id }}"
                                 class="fixed inset-0 z-50 hidden"
                                 data-attendance-modal
                                 data-session-id="{{ $session->id }}"
                                 data-learning-type="{{ $session->learning_type }}"
                                 data-location-lat="{{ $session->location?->latitude }}"
                                 data-location-lng="{{ $session->location?->longitude }}"
                                 data-location-radius="{{ $session->location?->radius_meters ?? 0 }}"
                                 data-location-name="{{ $session->location?->location_name }}">
                                <div class="absolute inset-0 bg-gray-900/60" data-attendance-modal-close="attendance-modal-{{ $session->id }}"></div>
                                <div class="relative z-10 flex min-h-screen items-start justify-center overflow-y-auto p-4">
                                    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
                                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                                            <div>
                                                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Presensi Mahasiswa</p>
                                                <h3 class="text-lg font-semibold text-gray-900">Form Presensi</h3>
                                                <p class="text-sm text-gray-500">{{ $session->course->name }} · {{ ucfirst($session->learning_type) }}</p>
                                            </div>
                                            <button type="button"
                                                    class="text-gray-400 hover:text-gray-600"
                                                    data-attendance-modal-close="attendance-modal-{{ $session->id }}">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <form action="{{ route('student.attendance.store', $session->id) }}"
                                              method="POST"
                                              enctype="multipart/form-data"
                                              class="max-h-[80vh] space-y-5 overflow-y-auto px-6 py-5">
                                            @csrf

                                            <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                                                Waktu presensi: <span class="font-semibold text-gray-900" id="attendance-time-{{ $session->id }}">Memuat...</span>
                                            </div>

                                            @if($session->session_token)
                                                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm space-y-3">
                                                    <div class="flex items-center gap-2">
                                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-50 text-slate-600">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8V6a4 4 0 10-8 0v2m-2 0h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2z"/></svg>
                                                        </span>
                                                        <div>
                                                            <p class="text-sm font-semibold text-gray-900">Token Presensi</p>
                                                            <p class="text-xs text-gray-500">Masukkan 6 karakter token. Kode didapatkan dari dosen.</p>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs font-medium" id="token-status-{{ $session->id }}"></p>
                                                    <input type="text"
                                                           name="token"
                                                           maxlength="6"
                                                           required
                                                           class="w-full rounded-lg border-gray-300 text-center text-lg font-mono uppercase tracking-[0.3em] focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5 px-4"
                                                           placeholder="------"
                                                           data-session-token="{{ $session->session_token }}"
                                                           data-session-id="{{ $session->id }}"
                                                           onkeyup="this.value = this.value.toUpperCase();">
                                                </div>
                                            @endif

                                            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm space-y-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </span>
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900">Status Kehadiran</p>
                                                        <p class="text-xs text-gray-500">Pilih status kehadiran Anda.</p>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
                                                        <input type="radio" name="status" value="present" checked>
                                                        Hadir
                                                    </label>
                                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
                                                        <input type="radio" name="status" value="permit">
                                                        Izin
                                                    </label>
                                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
                                                        <input type="radio" name="status" value="sick">
                                                        Sakit
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm space-y-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h11a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>
                                                    </span>
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900">Foto Presensi</p>
                                                        <p class="text-xs text-gray-500">Ambil foto realtime melalui kamera.</p>
                                                    </div>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 space-y-3">
                                                    <video id="camera-stream-{{ $session->id }}" class="w-full rounded-md bg-black hidden" playsinline></video>
                                                    <canvas id="camera-canvas-{{ $session->id }}" class="w-full rounded-md hidden"></canvas>
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <button type="button"
                                                                class="px-3 py-1.5 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700"
                                                                data-camera-start
                                                                data-session-id="{{ $session->id }}">
                                                            Buka Kamera
                                                        </button>
                                                        <button type="button"
                                                                class="px-3 py-1.5 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 hidden"
                                                                data-camera-capture
                                                                data-session-id="{{ $session->id }}">
                                                            Ambil Foto
                                                        </button>
                                                        <button type="button"
                                                                class="px-3 py-1.5 text-sm rounded-md border border-gray-200 text-gray-600 hover:bg-white hidden"
                                                                data-camera-retake
                                                                data-session-id="{{ $session->id }}">
                                                            Ulangi Foto
                                                        </button>
                                                    </div>
                                                    <p class="text-xs text-gray-500">Gunakan kamera perangkat. Foto akan diambil langsung tanpa upload manual.</p>
                                                    <input type="hidden" name="photo_base64" id="photoBase64-{{ $session->id }}">
                                                </div>
                                            </div>

                                        <div class="hidden rounded-xl border border-gray-100 bg-white p-4 shadow-sm" id="supporting-wrapper-{{ $session->id }}">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Sakit/Izin</label>
                                            <input type="file"
                                                   name="supporting_document"
                                                   accept="image/*,application/pdf"
                                                   class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2">
                                            <p class="text-xs text-gray-500 mt-2">Upload surat dokter/izin resmi (foto/PDF).</p>
                                        </div>

                                        @if($session->learning_type === 'offline')
                                            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm space-y-3" id="geo-wrapper-{{ $session->id }}">
                                                <div class="flex items-center gap-2">
                                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.866-4 6-4 6s-4-2.134-4-6a8 8 0 1116 0c0 3.866-4 6-4 6s-4-2.134-4-6z"/><circle cx="12" cy="11" r="3" stroke-width="2"/></svg>
                                                    </span>
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900">Geolokasi Presensi</p>
                                                        <p class="text-xs text-gray-500">Pastikan berada dalam radius kampus.</p>
                                                    </div>
                                                    </div>
                                                    <div id="map-{{ $session->id }}" class="h-64 rounded-lg border border-gray-200"></div>
                                                    <input type="hidden" name="latitude" id="latitudeField-{{ $session->id }}">
                                                    <input type="hidden" name="longitude" id="longitudeField-{{ $session->id }}">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="space-y-1">
                                                            <p class="text-xs text-gray-500" id="geo-coords-{{ $session->id }}"></p>
                                                            <p class="text-xs text-gray-500" id="geo-address-{{ $session->id }}"></p>
                                                        </div>
                                                    <button type="button"
                                                            class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition"
                                                            data-refresh-location
                                                            data-session-id="{{ $session->id }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.866-4 6-4 6s-4-2.134-4-6a8 8 0 1116 0c0 3.866-4 6-4 6s-4-2.134-4-6z"/>
                                                            <circle cx="12" cy="11" r="3" stroke-width="2"/>
                                                        </svg>
                                                        Gunakan Lokasi Saya Saat Ini
                                                    </button>
                                                    </div>
                                                    <p class="text-sm" id="geo-status-{{ $session->id }}"></p>
                                                </div>
                                            @endif

                                            <div class="flex flex-col sm:flex-row sm:justify-end gap-3 border-t border-gray-100 pt-4">
                                                <button type="button"
                                                        class="px-4 py-2 rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50"
                                                        data-attendance-modal-close="attendance-modal-{{ $session->id }}">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                        id="attendance-submit-{{ $session->id }}"
                                                        class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                                                    Kirim Presensi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        <div class="mt-6">
            {{ $activeSessionsPaginator->links() }}
        </div>
    @endif

</div>

@push('scripts')
<script>
    const attendanceModals = new Map();
    const cameraState = new Map();
    const showConfirm = (options) => {
        if (window.appConfirm) {
            return window.appConfirm(options);
        }
        return Promise.resolve(false);
    };

    function formatNow() {
        return new Intl.DateTimeFormat('id-ID', {
            dateStyle: 'full',
            timeStyle: 'short'
        }).format(new Date());
    }

    function setTime(sessionId) {
        const timeEl = document.getElementById(`attendance-time-${sessionId}`);
        if (timeEl) {
            timeEl.textContent = formatNow();
        }
    }

    function getDistanceMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
    }

    function initMap(modalEl, sessionId) {
        if (!modalEl || modalEl.dataset.learningType !== 'offline') {
            return;
        }

        if (attendanceModals.has(sessionId)) {
            const mapInstance = attendanceModals.get(sessionId);
            setTimeout(() => mapInstance.map.invalidateSize(), 150);
            return;
        }

        const mapEl = document.getElementById(`map-${sessionId}`);
        if (!mapEl || typeof L === 'undefined') {
            return;
        }

        const classLat = parseFloat(modalEl.dataset.locationLat || '0');
        const classLng = parseFloat(modalEl.dataset.locationLng || '0');
        const radius = parseFloat(modalEl.dataset.locationRadius || '0');
        const statusEl = document.getElementById(`geo-status-${sessionId}`);
        const coordsEl = document.getElementById(`geo-coords-${sessionId}`);
        const addressEl = document.getElementById(`geo-address-${sessionId}`);
        const submitBtn = document.getElementById(`attendance-submit-${sessionId}`);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
        }
        if (statusEl) {
            statusEl.textContent = 'Mengambil lokasi...';
            statusEl.className = 'text-sm text-gray-500';
        }

        const map = L.map(mapEl).setView([classLat, classLng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const classMarker = L.marker([classLat, classLng]).addTo(map);
        if (modalEl.dataset.locationName) {
            classMarker.bindPopup(modalEl.dataset.locationName);
        }

        L.circle([classLat, classLng], {
            radius: radius,
            color: '#ef4444',
            fillColor: '#fecaca',
            fillOpacity: 0.3
        }).addTo(map);

        let watchId = null;

        function stopWatch() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
        }

        function requestLocation() {
            const geoOptions = {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            };

            if (statusEl) {
                statusEl.textContent = 'Mengambil lokasi...';
                statusEl.className = 'text-sm text-gray-500';
            }
            if (addressEl) {
                addressEl.textContent = '';
            }

            const onSuccess = function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                document.getElementById(`latitudeField-${sessionId}`).value = lat;
                document.getElementById(`longitudeField-${sessionId}`).value = lng;
                if (coordsEl) {
                    coordsEl.textContent = `Lokasi Anda: ${lat.toFixed(6)}, ${lng.toFixed(6)} (akurasi ±${Math.round(accuracy)}m)`;
                }
                if (addressEl) {
                    addressEl.textContent = 'Mengambil nama lokasi...';
                }

                if (mapInstance.studentMarker) {
                    mapInstance.studentMarker.setLatLng([lat, lng]);
                } else {
                    mapInstance.studentMarker = L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Anda');
                }
                map.setView([lat, lng], 18);

                const distance = getDistanceMeters(lat, lng, classLat, classLng);
                if (distance <= radius) {
                    statusEl.textContent = 'Anda berada dalam radius presensi.';
                    statusEl.className = 'text-sm text-green-600';
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    modalEl.dataset.inRadius = 'true';
                } else {
                    statusEl.textContent = 'Anda berada di luar area kampus.';
                    statusEl.className = 'text-sm text-red-600';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    modalEl.dataset.inRadius = 'false';
                }

                if (addressEl) {
                    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`;
                    fetch(url)
                        .then(response => response.ok ? response.json() : null)
                        .then(data => {
                            if (!data) {
                                addressEl.textContent = 'Nama lokasi tidak tersedia.';
                                return;
                            }
                            const road = data.address?.road;
                            const suburb = data.address?.suburb;
                            const city = data.address?.city || data.address?.town || data.address?.village;
                            const parts = [road, suburb, city].filter(Boolean);
                            if (parts.length) {
                                addressEl.textContent = `Nama jalan: ${parts.join(', ')}`;
                                return;
                            }
                            if (data.display_name) {
                                addressEl.textContent = `Alamat: ${data.display_name}`;
                                return;
                            }
                            addressEl.textContent = 'Nama lokasi tidak tersedia.';
                        })
                        .catch(() => {
                            addressEl.textContent = 'Gagal memuat nama lokasi.';
                        });
                }
                if (accuracy <= 50) {
                    stopWatch();
                }
            };

            const onError = function() {
                if (statusEl) {
                    statusEl.textContent = 'Gagal mendapatkan lokasi. Pastikan GPS aktif.';
                    statusEl.className = 'text-sm text-red-600';
                }
                if (coordsEl) {
                    coordsEl.textContent = '';
                }
                if (addressEl) {
                    addressEl.textContent = '';
                }
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                }
                modalEl.dataset.inRadius = 'false';
                stopWatch();
            };

            stopWatch();
            navigator.geolocation.getCurrentPosition(onSuccess, onError, geoOptions);
            watchId = navigator.geolocation.watchPosition(onSuccess, onError, geoOptions);
        }

        const mapInstance = { map, studentMarker: null, requestLocation };
        attendanceModals.set(sessionId, mapInstance);
        setTimeout(() => map.invalidateSize(), 150);
        requestLocation();
    }

    function toggleSupporting(sessionId, value) {
        const wrapper = document.getElementById(`supporting-wrapper-${sessionId}`);
        if (!wrapper) return;
        const input = wrapper.querySelector('input[name="supporting_document"]');
        const shouldShow = value === 'permit' || value === 'sick';
        wrapper.classList.toggle('hidden', !shouldShow);
        if (input) {
            input.required = shouldShow;
        }

        const geoWrapper = document.getElementById(`geo-wrapper-${sessionId}`);
        if (geoWrapper) {
            const shouldHideGeo = shouldShow;
            geoWrapper.classList.toggle('hidden', shouldHideGeo);
            if (shouldHideGeo) {
                const submitBtn = document.getElementById(`attendance-submit-${sessionId}`);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                }
            }
        }
    }

    document.querySelectorAll('[data-attendance-modal-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modalId = trigger.dataset.attendanceModalTrigger;
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.remove('hidden');
            const sessionId = modal.dataset.sessionId;
            setTime(sessionId);
            initMap(modal, sessionId);
            toggleSupporting(sessionId, modal.querySelector('input[name="status"]:checked')?.value || 'present');
        });
    });

    document.querySelectorAll('[data-attendance-modal-close]').forEach((closer) => {
        closer.addEventListener('click', () => {
            const modalId = closer.dataset.attendanceModalClose;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
            const sessionId = modal?.dataset.sessionId;
            const camera = cameraState.get(sessionId);
            if (camera?.stream) {
                camera.stream.getTracks().forEach(track => track.stop());
                camera.stream = null;
            }
        });
    });

    document.querySelectorAll('[data-attendance-modal]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
        modal.querySelectorAll('input[name="status"]').forEach((input) => {
            input.addEventListener('change', (event) => {
                toggleSupporting(modal.dataset.sessionId, event.target.value);
            });
        });

        const form = modal.querySelector('form');
        if (form) {
            form.addEventListener('submit', async (event) => {
                const statusValue = form.querySelector('input[name="status"]:checked')?.value || 'present';
                if (modal.dataset.learningType === 'offline' && statusValue === 'present' && modal.dataset.inRadius !== 'true') {
                    event.preventDefault();
                    window.alert('Tidak dapat melaksanakan presensi, anda di luar jangkauan.');
                    return;
                }

                const photoBase64 = form.querySelector('input[name="photo_base64"]')?.value;
                if (!photoBase64) {
                    event.preventDefault();
                    window.alert('Silakan ambil foto presensi terlebih dahulu.');
                    return;
                }
                event.preventDefault();
                const proceed = await showConfirm({
                    title: 'Kirim Presensi',
                    message: 'Kirim presensi sekarang? Pastikan data sudah benar.',
                    confirmText: 'Kirim'
                });
                if (proceed) {
                    form.submit();
                }
            });
        }
    });

    function resetCameraUI(sessionId, camera) {
        const streamEl = document.getElementById(`camera-stream-${sessionId}`);
        const canvasEl = document.getElementById(`camera-canvas-${sessionId}`);
        const captureBtn = document.querySelector(`[data-camera-capture][data-session-id="${sessionId}"]`);
        const retakeBtn = document.querySelector(`[data-camera-retake][data-session-id="${sessionId}"]`);

        if (streamEl) {
            streamEl.classList.remove('hidden');
        }
        if (canvasEl) {
            canvasEl.classList.add('hidden');
        }
        if (captureBtn) captureBtn.classList.remove('hidden');
        if (retakeBtn) retakeBtn.classList.add('hidden');

        if (camera?.stream) {
            camera.stream.getTracks().forEach(track => track.stop());
            camera.stream = null;
        }
    }

    async function startCamera(sessionId) {
        const streamEl = document.getElementById(`camera-stream-${sessionId}`);
        const canvasEl = document.getElementById(`camera-canvas-${sessionId}`);
        const captureBtn = document.querySelector(`[data-camera-capture][data-session-id="${sessionId}"]`);
        const retakeBtn = document.querySelector(`[data-camera-retake][data-session-id="${sessionId}"]`);

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            window.alert('Perangkat tidak mendukung akses kamera.');
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            if (streamEl) {
                streamEl.srcObject = stream;
                await streamEl.play();
                streamEl.classList.remove('hidden');
            }
            if (canvasEl) {
                canvasEl.classList.add('hidden');
            }
            cameraState.set(sessionId, { stream });
            if (captureBtn) captureBtn.classList.remove('hidden');
            if (retakeBtn) retakeBtn.classList.add('hidden');
        } catch (error) {
            window.alert('Tidak bisa mengakses kamera. Periksa izin kamera di perangkat Anda.');
        }
    }

    document.querySelectorAll('[data-camera-start]').forEach((button) => {
        button.addEventListener('click', async () => {
            const sessionId = button.dataset.sessionId;
            await startCamera(sessionId);
        });
    });

    document.querySelectorAll('[data-camera-capture]').forEach((button) => {
        button.addEventListener('click', async () => {
            const sessionId = button.dataset.sessionId;
            const streamEl = document.getElementById(`camera-stream-${sessionId}`);
            const canvasEl = document.getElementById(`camera-canvas-${sessionId}`);
            const captureBtn = document.querySelector(`[data-camera-capture][data-session-id="${sessionId}"]`);
            const retakeBtn = document.querySelector(`[data-camera-retake][data-session-id="${sessionId}"]`);
            const photoInput = document.getElementById(`photoBase64-${sessionId}`);

            if (!streamEl || !canvasEl) return;

            const width = streamEl.videoWidth || 1280;
            const height = streamEl.videoHeight || 720;
            canvasEl.width = width;
            canvasEl.height = height;
            const ctx = canvasEl.getContext('2d');
            ctx.drawImage(streamEl, 0, 0, width, height);

            const dataUrl = canvasEl.toDataURL('image/jpeg', 0.9);
            if (photoInput) {
                photoInput.value = dataUrl;
            }

            canvasEl.classList.remove('hidden');
            streamEl.classList.add('hidden');
            if (captureBtn) captureBtn.classList.add('hidden');
            if (retakeBtn) retakeBtn.classList.remove('hidden');

            const camera = cameraState.get(sessionId);
            if (camera?.stream) {
                camera.stream.getTracks().forEach(track => track.stop());
                camera.stream = null;
            }

            const confirmed = await showConfirm({
                title: 'Foto Tersimpan',
                message: 'Foto sudah direkam dan siap dikirim. Gunakan foto ini?',
                confirmText: 'Gunakan Foto'
            });
            if (!confirmed) {
                if (photoInput) {
                    photoInput.value = '';
                }
                resetCameraUI(sessionId, cameraState.get(sessionId));
                startCamera(sessionId);
            }
        });
    });

    document.querySelectorAll('[data-camera-retake]').forEach((button) => {
        button.addEventListener('click', () => {
            const sessionId = button.dataset.sessionId;
            const photoInput = document.getElementById(`photoBase64-${sessionId}`);
            if (photoInput) {
                photoInput.value = '';
            }
            resetCameraUI(sessionId, cameraState.get(sessionId));
            startCamera(sessionId);
        });
    });

    document.querySelectorAll('[data-refresh-location]').forEach((button) => {
        button.addEventListener('click', () => {
            const sessionId = button.dataset.sessionId;
            const instance = attendanceModals.get(sessionId);
            if (instance?.requestLocation) {
                instance.requestLocation();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const sessionId = params.get('session');
        if (!sessionId) return;
        const trigger = document.querySelector(`[data-attendance-modal-trigger="attendance-modal-${sessionId}"]`);
        if (trigger) {
            trigger.click();
        }
    });

    document.querySelectorAll('input[name="token"][data-session-token]').forEach((input) => {
        const sessionId = input.dataset.sessionId;
        const statusEl = document.getElementById(`token-status-${sessionId}`);
        const expected = input.dataset.sessionToken || '';

        const setStatus = (valid) => {
            if (!statusEl) return;
            if (valid === null) {
                statusEl.textContent = '';
                statusEl.className = 'text-xs font-medium';
                input.classList.remove('border-red-500', 'border-green-500');
                return;
            }
            if (valid) {
                statusEl.textContent = 'Kode valid';
                statusEl.className = 'text-xs font-medium text-emerald-600';
                input.classList.remove('border-red-500');
                input.classList.add('border-green-500');
            } else {
                statusEl.textContent = 'Kode tidak valid';
                statusEl.className = 'text-xs font-medium text-red-600';
                input.classList.remove('border-green-500');
                input.classList.add('border-red-500');
            }
        };

        setStatus(null);
        input.addEventListener('input', () => {
            const value = input.value.toUpperCase().trim();
            if (!value) {
                setStatus(null);
                return;
            }
            setStatus(value === expected);
        });
    });
</script>
@endpush

@endsection
