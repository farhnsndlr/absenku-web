# 🚀 AbsenKu (Monolith Edition)
### Sistem Absensi Perkuliahan Cerdas Berbasis Geofencing & Wajah

![Project Banner](https://placehold.co/1200x400/3b82f6/ffffff?text=AbsenKu+Project+Banner&font=roboto)

---

> **Status Proyek:** 🔥 Dalam Pengembangan Aktif | **Target Rilis:** Vercel & Railway

**AbsenKu** adalah solusi modern untuk mendigitalkan kehadiran di lingkungan kampus. Lupakan titip absen! Sistem kami memvalidasi kehadiran mahasiswa secara *real-time* menggunakan kombinasi **Lokasi GPS (Geofencing)** dan **Bukti Foto Wajah**.

Dibangun dengan arsitektur **Monolithic Laravel** yang tangguh, proyek ini mengutamakan kecepatan pengembangan dan kemudahan *deployment* tanpa mengorbankan fitur canggih.

---

# ✨ Fitur Unggulan

Kami merancang pengalaman pengguna yang spesifik untuk setiap peran di kampus.

## 🎓 Mahasiswa: Cepat & Akurat (Mobile-First)
* **📱 Dashboard Ringkas:** Akses cepat ke ringkasan kehadiran dan daftar sesi kuliah yang sedang aktif hari ini.
* **📸 Smart Check-in:** Presensi satu tombol yang terintegrasi langsung dengan kamera dan GPS smartphone.
* **📍 Geofence Validator:** Sistem otomatis menolak presensi jika mahasiswa berada di luar radius lokasi kampus yang ditentukan dosen (untuk sesi onsite).

## 👨‍🏫 Dosen: Kendali Penuh Sesi
* **📅 Manajemen Sesi Fleksibel:** Buat sesi perkuliahan tipe **Onsite** (wajib di lokasi) atau **Online** (bebas lokasi).
* **📊 Monitoring Real-time:** Pantau siapa yang sudah hadir, lengkap dengan bukti foto dan lokasi mereka saat absen.

## 👮‍♂️ Admin: Manajemen Terpusat
* **🏢 Master Data Control:** Kelola data Pengguna (Dosen/Mahasiswa), Mata Kuliah, dan titik koordinat Lokasi Kampus dengan mudah.

---

# 🛠️ Di Balik Layar: Tech Stack

Kami menggunakan kombinasi teknologi yang matang dan modern untuk performa maksimal.

| Kategori | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Fullstack Framework** | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white) | Laravel 10/11 sebagai otak utama aplikasi (Backend logic, routing, ORM). |
| **Frontend Engine** | ![Blade](https://img.shields.io/badge/Blade-FF2D20?style=flat-square&logo=laravel&logoColor=white) | Laravel Blade Templates untuk merender tampilan HTML di sisi server. |
| **Styling** | ![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white) | Utility-first CSS framework untuk desain UI yang cepat, modern, dan responsif. |
| **Database (Prod)** | ![MySQL](https://img.shields.io/badge/MySQL-005C84?style=flat-square&logo=mysql&logoColor=white) | Database relasional yang di-hosting di **Railway** (digunakan sejak development). |
| **Media Storage** | ![Cloudinary](https://img.shields.io/badge/Cloudinary-3448C5?style=flat-square&logo=cloudinary&logoColor=white) | Penyimpanan cloud yang aman dan cepat untuk bukti foto kehadiran. |
| **Hardware Access** | ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) | Native Browser API untuk mengakses Kamera dan Geolocation pengguna. |
| **Deployment Target** | ![Vite](https://img.shields.io/badge/Vercel-000000?style=flat-square&logo=vercel&logoColor=white) | Aplikasi akan di-deploy ke **Vercel** untuk performa global yang cepat. |

---

# ⚙️ Panduan Instalasi Lokal (Getting Started)

Ikuti langkah ini untuk menjalankan proyek di komputer Anda.

### Prasyarat
* PHP >= 8.1 & Composer
* Node.js & NPM
* Git
* Koneksi Internet Stabil (Wajib untuk akses Database Railway & Cloudinary)

### Langkah-langkah

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/farhnsndlr/absenku-monolith.git](https://github.com/farhnsndlr/absenku-monolith.git)
    cd absenku-monolith
    ```

2.  **Install Dependencies**
    ```bash
    composer install && npm install
    ```

3.  **Konfigurasi Environment (.env)**
    Salin file contoh dan generate key.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    > **🚨 PENTING:** Buka file `.env`. Anda **WAJIB** meminta kredensial berikut kepada Project Lead (Farhan) dan mengisinya agar aplikasi bisa berjalan:
    > * `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (Koneksi Railway)
    > * `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`

4.  **Setup Database (Remote)**
    Karena menggunakan database remote, perintah ini akan mereset dan mengisi data di server Railway.
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Jalankan Aplikasi**
    Jalankan dua terminal terpisah:
    *Terminal 1 (Laravel):* `php artisan serve`
    *Terminal 2 (Vite):* `npm run dev`
    Akses di `http://localhost:8000`.

---

# 🧪 Akun Testing Siap Pakai

Gunakan kredensial ini setelah menjalankan *seeder* untuk masuk ke sistem:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@absenku.com` | `password123` |
| **Dosen** | `dosen@absenku.com` | `password123` |
| **Mahasiswa** | `mahasiswa@absenku.com`| `password123` |

---

# 🌍 Akses Website 
Website ini dapat diakses dengan menggunakan link :

---

# 👥 Tim Pengembang

Dibangun dengan semangat dan kopi oleh tim kecil yang berdedikasi:

* **Farhan** - Fullstack Lead, Logic Master & Deployment Ops
* **Abib** - Frontend Specialist (Student UI & Layouts)
* **Justin** - Frontend Specialist (Admin & Lecturer UI)
---
© 2025 AbsenKu Team.
