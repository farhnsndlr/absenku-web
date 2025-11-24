<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Absen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-xl overflow-hidden shadow-2xl relative">

        <div class="relative bg-black h-96 w-full flex items-center justify-center">
            <video id="camera-stream" autoplay playsinline class="w-full h-full object-cover"></video>
            <p class="absolute text-white text-sm opacity-50">Kamera Area</p>
        </div>

        <div class="p-6 text-center space-y-4">

            <h2 class="text-lg font-bold text-gray-700">Absensi Hari Ini</h2>

            <button type="button" id="btn-capture" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transform transition active:scale-95">
                📷 Ambil Foto
            </button>

            <form action="" method="POST" id="attendance-form" class="mt-4">
                @csrf

                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="long" id="long">

                <input type="hidden" name="photo" id="photo">

                <button type="submit" class="hidden w-full bg-green-500 text-white py-2 rounded-lg">
                    Kirim Absen
                </button>
            </form>

        </div>
    </div>

</body>
</html>
