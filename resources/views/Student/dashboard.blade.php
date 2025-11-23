<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-lg overflow-hidden">

        <div class="bg-blue-600 p-6 text-white rounded-b-3xl">
            <h1 class="text-2xl font-bold">Halo, Mahasiswa! 👋</h1>
            <p class="text-sm opacity-90">Selamat datang di Absenku</p>
        </div>

        <div class="p-6 grid grid-cols-2 gap-4">
            <a href="{{ route('student.attendance') }}" class="bg-blue-50 p-4 rounded-xl shadow-sm border border-blue-100 flex flex-col items-center hover:bg-blue-100 transition">
                <span class="text-3xl">📷</span>
                <span class="mt-2 font-semibold text-gray-700">Absen Masuk</span>
            </a>

            <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-100 flex flex-col items-center">
                <span class="text-3xl">📅</span>
                <span class="mt-2 font-semibold text-gray-700">Riwayat</span>
            </div>
        </div>

    </div>

</body>
</html>
