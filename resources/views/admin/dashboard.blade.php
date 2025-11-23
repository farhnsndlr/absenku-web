@extends('layouts.dashboard')

@section('title', 'Dashboard Superadmin')
@section('page-title', 'Dasboard Superadmin')

@section('navigation')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-blue-600 bg-blue-50 rounded-lg font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Beranda</span>
    </a>

    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <span>Pengguna</span>
    </a>

    <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <span>Mata Kuliah</span>
    </a>

    <a href="{{ route('admin.locations.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Lokasi</span>
    </a>

    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>Laporan</span>
    </a>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Total Pengguna Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-2">Total Pengguna</p>
                    <h3 class="text-4xl font-bold text-gray-900">{{ number_format($stats['total_students'] + $stats['total_lecturers']) }}</h3>
                </div>
                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                <span class="font-medium text-blue-600">{{ number_format($stats['total_students']) }}</span> Mahasiswa •
                <span class="font-medium text-green-600">{{ number_format($stats['total_lecturers']) }}</span> Dosen
            </p>
        </div>

        <!-- Total Lokasi Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-2">Total Lokasi</p>
                    <h3 class="text-4xl font-bold text-gray-900">{{ $stats['total_locations'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                <span class="font-medium text-purple-600">{{ number_format($stats['total_courses']) }}</span> Mata Kuliah Aktif
            </p>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Lalu Lintas Penggunaan Sistem</h3>
            <p class="text-sm text-gray-600">Pengguna aktif bulanan dan permintaan API</p>
        </div>

        <!-- Chart Canvas -->
        <div class="h-80 relative">
            <canvas id="usageChart"></canvas>
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-center gap-8 mt-6 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                <span class="text-sm text-gray-700">Pengguna Aktif</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                <span class="text-sm text-gray-700">Permintaan API</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data dummy untuk chart (nanti bisa diganti dengan data real dari backend)
    const chartData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu'],
        activeUsers: [300, 380, 320, 450, 550, 420, 710, 870],
        apiRequests: [400, 300, 350, 550, 450, 610, 500, 790]
    };

    const ctx = document.getElementById('usageChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Pengguna Aktif',
                    data: chartData.activeUsers,
                    borderColor: '#f97316',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                },
                {
                    label: 'Permintaan API',
                    data: chartData.apiRequests,
                    borderColor: '#14b8a6',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#14b8a6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: {
                        display: false
                    },
                    grid: {
                        color: '#f3f4f6',
                        drawTicks: false
                    },
                    ticks: {
                        padding: 10,
                        color: '#6b7280',
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    border: {
                        display: false
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        padding: 10,
                        color: '#6b7280',
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
