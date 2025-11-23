<x-layouts.dashboard>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Dosen Dashboard</h2>
        <p class="text-gray-500 text-sm mt-1">Overview data kehadiran dan informasi penting.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Sesi Diampu</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">12</h3>
            <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                <span class="bg-green-100 px-1 rounded">↑ 2 Sesi</span> Minggu Ini
            </p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Persentase Kehadiran Mhs</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">88.5%</h3>
            <p class="text-xs text-gray-500 mt-1">Target 90% tercapai</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Tren Kehadiran Mingguan</h3>
        <p class="text-sm text-gray-500 mb-6">Ringkasan rata-rata persentase kehadiran mahasiswa per hari.</p>

        <div class="h-80 bg-gray-50 rounded-lg flex items-end justify-between p-4" style="--tw-ring-offset-width: 0px;">
            @php
                $attendances = [80, 78, 90, 75, 82, 65]; 
                $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            @endphp

            @foreach ($attendances as $key => $percent)
            <div class="flex flex-col items-center justify-end h-full w-1/6 px-1">
                <div style="height: {{ $percent }}%;" class="w-full bg-blue-500 rounded-t-md transition-all duration-300"></div>
                <span class="mt-2 text-xs text-gray-600">{{ $days[$key] }}</span>
            </div>
            @endforeach
        </div>
        <div class="text-center text-xs text-gray-500 mt-4">Rata-rata Kehadiran</div>
    </div>

</x-layouts.dashboard>