<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AbsenKu') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-absenku.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .float-soft {
            animation: floatSoft 7s ease-in-out infinite;
        }

        @keyframes floatSoft {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">

    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('images/logo-absenku.png') }}" alt="Logo AbsenKu" class="w-10 h-10">
                        <span class="ml-2 text-3xl font-extrabold text-blue-600">AbsenKu</span>
                    </a>
                </div>

                <nav class="hidden md:flex space-x-10">
                    <a href="#" class="text-base font-medium text-gray-500 hover:text-blue-600 transition">Beranda</a>
                    <a href="#features" class="text-base font-medium text-gray-500 hover:text-blue-600 transition">Fitur</a>
                    <a href="#cara-kerja" class="text-base font-medium text-gray-500 hover:text-blue-600 transition">Cara Kerja</a>
                    <a href="#kontak" class="text-base font-medium text-gray-500 hover:text-blue-600 transition">Kontak</a>
                </nav>

                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        {{-- Logika untuk menentukan rute dashboard berdasarkan peran --}}
                        @php
                            $dashboardRoute = match(auth()->user()->role) {
                                'admin' => 'admin.dashboard',
                                'lecturer' => 'lecturer.dashboard',
                                'student' => 'student.dashboard',
                                default => 'login', // Cadangan jika role tidak dikenali
                            };
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="text-base font-medium text-white bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-base font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-6 py-3 rounded-lg border border-blue-200 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="text-base font-medium text-white bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg shadow transition">
                            Daftar
                        </a>
                    @endauth
                </div>
                 </div>
        </div>
    </header>

    <main>
        <section class="relative pt-20 pb-24 lg:pt-32 lg:pb-40 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="reveal" data-animate data-delay="0">
                        <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-gray-900 leading-tight mb-6">
                            Permudah Pencacatan Kehadiran demi <span class="text-blue-600">Prestasi Akademik</span>
                        </h1>
                        <p class="text-lg md:text-xl text-gray-600 mb-10 leading-relaxed">
                            AbsenKu adalah sistem manajemen absensi modern yang intuitif untuk lingkungan kampus. Sederhanakan proses admnistrasi, tingktkan akurasi, serta berdayakan mahasiswa dan dosen dengan informasi kehadiran secara real-time.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-xl transition duration-300">
                                Coba Sekarang Gratis
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#features" class="inline-flex justify-center items-center px-8 py-4 text-lg font-bold text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition duration-300">
                                Pelajari Fitur
                            </a>
                        </div>
                    </div>

                    <div class="relative lg:ml-10 reveal" data-animate data-delay="120">
                        <img src="https://placehold.co/600x500/e0e7ff/3b82f6?text=Ilustrasi+Absen+HP&font=roboto" alt="Ilustrasi Absensi Mobile" class="w-full h-auto rounded-3xl shadow-2xl transform hover:scale-105 transition duration-500 float-soft">

                        <div class="absolute -top-20 -right-20 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -z-10 animate-blob"></div>
                        <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -z-10 animate-blob animation-delay-2000"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="py-24 bg-gray-50 relative">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-20 reveal" data-animate>
                    <p class="text-4xl font-extrabold text-gray-900 mb-6">
                        Dirancang untuk Semua Peran di Kampus
                    </p>
                    <p class="text-xl text-gray-600">
                        Kami merancang pengalaman pengguna yang spesifik dan optimal untuk Mahasiswa, Dosen, dan Administrator.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                    <div class="bg-white rounded-3xl p-10 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 group reveal" data-animate data-delay="0">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Smart Check-in Mahasiswa</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Presensi sekali tap di HP. Validasi otomatis menggunakan lokasi GPS (Geofence) dan verifikasi foto wajah secara real-time. Cepat dan anti-titip absen.
                        </p>
                    </div>

                    <div class="bg-white rounded-3xl p-10 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 group reveal" data-animate data-delay="120">
                        <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0a2.25 2.25 0 002.25 2.25h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Manajemen Sesi Dosen</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Buat sesi perkuliahan Onsite (wajib di lokasi kampus) atau Online dengan mudah. Pantau kehadiran dan bukti foto mahasiswa secara langsung di dashboard.
                        </p>
                    </div>

                    <div class="bg-white rounded-3xl p-10 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 group reveal" data-animate data-delay="240">
                        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Kontrol Data Terpusat</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Dashboard admin yang lengkap untuk mengelola data master: Pengguna, Mata Kuliah, dan titik koordinat Lokasi Kampus (Geofencing) dalam satu tempat.
                        </p>
                    </div>

                </div>
             </div>
        </section>

        <section id="cara-kerja" class="py-24 bg-white relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center max-w-3xl mx-auto mb-20">
                    <p class="text-4xl font-extrabold text-gray-900 mb-6">
                        Cara Kerja AbsenKu
                    </p>
                    <p class="text-xl text-gray-600">
                        Proses sederhana namun lengkap, dirancang untuk memastikan transparansi dan akurasi absensi.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">

                    <div class="flex flex-col items-center text-center hover:-translate-y-1 transition-all duration-300">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold text-xl mb-6 shadow-md">
                            1
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-3">Registrasi & Pengaturan</h3>
                        <p class="text-gray-600 leading-relaxed max-w-xs">
                            Superadmin dan mahasiswa dapat membuat akun sendiri, sedangkan akun dosen dibuat oleh pihak superadmin.
                        </p>
                    </div>

                    <div class="flex flex-col items-center text-center hover:-translate-y-1 transition-all duration-300">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold text-xl mb-6 shadow-md">
                            2
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-3">Isi Absensi</h3>
                        <p class="text-gray-600 leading-relaxed max-w-xs">
                            Mahasiswa melakukan check-in untuk setiap sesi perkuliahan. Dosen mengelola serta memantau absensi secara real-time.
                        </p>
                    </div>

                    <div class="flex flex-col items-center text-center hover:-translate-y-1 transition-all duration-300">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold text-xl mb-6 shadow-md">
                            3
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-3">Pembuatan Laporan</h3>
                        <p class="text-gray-600 leading-relaxed max-w-xs">
                            Akses analitik absensi yang detail serta wawasan kinerja untuk berbagai kebutuhan.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-16">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-10 reveal" data-animate>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Our Teams</h2>
                    <p class="text-gray-600 mt-3 max-w-2xl mx-auto">
                        Tim kecil dengan fokus besar: membangun pengalaman presensi yang rapi, cepat, dan dapat diandalkan.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center mx-auto w-full max-w-xs reveal transition duration-500 ease-out hover:-translate-y-1.5 hover:shadow-2xl" data-animate data-delay="0">
                        <div class="mx-auto w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-xl font-bold text-blue-700">
                            AR
                        </div>
                        <h4 class="mt-4 font-bold text-gray-900">Alya Rahma</h4>
                        <p class="text-sm text-gray-500">Product Design</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center mx-auto w-full max-w-xs reveal transition duration-500 ease-out hover:-translate-y-1.5 hover:shadow-2xl" data-animate data-delay="120">
                        <div class="mx-auto w-24 h-24 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center text-xl font-bold text-emerald-700">
                            DN
                        </div>
                        <h4 class="mt-4 font-bold text-gray-900">Dimas Nugraha</h4>
                        <p class="text-sm text-gray-500">Frontend Dev</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center mx-auto w-full max-w-xs reveal transition duration-500 ease-out hover:-translate-y-1.5 hover:shadow-2xl" data-animate data-delay="240">
                        <div class="mx-auto w-24 h-24 rounded-full bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center text-xl font-bold text-amber-700">
                            KS
                        </div>
                        <h4 class="mt-4 font-bold text-gray-900">Kirana Sari</h4>
                        <p class="text-sm text-gray-500">Backend Dev</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center mx-auto w-full max-w-xs reveal transition duration-500 ease-out hover:-translate-y-1.5 hover:shadow-2xl" data-animate data-delay="360">
                        <div class="mx-auto w-24 h-24 rounded-full bg-gradient-to-br from-rose-100 to-rose-200 flex items-center justify-center text-xl font-bold text-rose-700">
                            FR
                        </div>
                        <h4 class="mt-4 font-bold text-gray-900">Fahri Ramadhan</h4>
                        <p class="text-sm text-gray-500">Data & QA</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center mx-auto w-full max-w-xs sm:col-span-2 lg:col-span-1 lg:col-start-3 reveal transition duration-500 ease-out hover:-translate-y-1.5 hover:shadow-2xl" data-animate data-delay="480">
                        <div class="mx-auto w-24 h-24 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center text-xl font-bold text-purple-700">
                            NP
                        </div>
                        <h4 class="mt-4 font-bold text-gray-900">Nadia Putri</h4>
                        <p class="text-sm text-gray-500">Project Lead</p>
                    </div>
                </div>
            </div>
        </section>

        </main>

    </body>

    <footer id="kontak" class="bg-white border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-6 py-12 grid md:grid-cols-4 gap-10 text-gray-700">

            <div>
            <div class="flex items-center space-x-2 mb-3">
                <img src="{{ asset('images/logo-absenku.png') }}" alt="Logo AbsenKu" class="wd-10 h-10">
                <span class="font-bold text-xl text-blue-700">AbsenKu</span>
            </div>
            <p class="text-sm leading-relaxed text-gray-600 max-w-xs">
                AbsenKu menyederhanakan pengelolaan absensi akademik untuk perguruan tinggi, menjamin efisiensi dan akurasi.
            </p>

            <div class="flex space-x-4 mt-4 text-gray-500 text-lg">
                <a href="#" class="hover:text-gray-700"><i class="fab fa-facebook"></i></a>
                <a href="#" class="hover:text-gray-700"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-gray-700"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-gray-700"><i class="fab fa-linkedin"></i></a>
            </div>
            </div>

            <div>
            <h3 class="font-semibold mb-4">Perusahaan</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-gray-900">Tentang kami</a></li>
                <li><a href="#" class="hover:text-gray-900">Karir</a></li>
                <li><a href="#" class="hover:text-gray-900">Media</a></li>
            </ul>
            </div>

            <div>
            <h3 class="font-semibold mb-4">Sumber daya</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-gray-900">Blog</a></li>
                <li><a href="#" class="hover:text-gray-900">Dukungan</a></li>
                <li><a href="#" class="hover:text-gray-900">Pengembang</a></li>
            </ul>
            </div>

            <div>
            <h3 class="font-semibold mb-4">Bantuan</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-gray-900">Pusat bantuan</a></li>
                <li><a href="#" class="hover:text-gray-900">Hubungi kami</a></li>
            </ul>
            </div>

        </div>

    <div class="border-t border-gray-200 text-center py-4 text-sm text-gray-500">
        © 2025 AbsenKu. All rights reserved.
    </div>
</footer>

<button id="scroll-to-top"
        type="button"
        class="fixed bottom-5 right-5 z-[60] hidden h-11 w-11 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    <span class="sr-only">Scroll ke atas</span>
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
    </svg>
</button>

<script>
    const scrollButton = document.getElementById('scroll-to-top');
    if (scrollButton) {
        const toggleScrollButton = () => {
            if (window.scrollY > 320) {
                scrollButton.classList.remove('hidden');
                scrollButton.classList.add('flex');
            } else {
                scrollButton.classList.add('hidden');
                scrollButton.classList.remove('flex');
            }
        };
        toggleScrollButton();
        window.addEventListener('scroll', toggleScrollButton);
        scrollButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    const animatedNodes = document.querySelectorAll('[data-animate]');
    if (animatedNodes.length) {
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const node = entry.target;
                const delay = Number(node.dataset.delay || 0);
                node.style.transitionDelay = `${delay}ms`;
                node.classList.add('is-visible');
                currentObserver.unobserve(node);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });

        animatedNodes.forEach((node) => observer.observe(node));
    }
</script>


</html>
