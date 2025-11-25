<?php

use Illuminate\Support\Facades\Route;
// Import semua Controller yang dibutuhkan
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LecturerDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LecturerSessionController;

// ====================================================
// RUTE PUBLIC - Landing Page
// ====================================================
Route::view('/', 'landing')->name('landing');


// ====================================================
// RUTE GUEST (Hanya untuk yang belum login)
// ====================================================
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register Routes
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


// ====================================================
// RUTE AUTHENTICATED (Harus login dulu)
// ====================================================
Route::middleware('auth')->group(function () {

    // --- Global Authenticated Routes (Bisa diakses semua role) ---

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifikasi
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // --- Group Route Profil ---
    Route::prefix('profile')->name('profile.')->group(function () {
        // Halaman Profil Saya
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        // Form Edit Profil
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        // Proses Update Profil
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        // Form Ubah Password
        Route::get('/password', [ProfileController::class, 'editPassword'])->name('password');
        // Proses Update Password
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });


    // --- Role Based Routes ---

    // 1. GROUP: ADMIN (Role: admin)
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin') // Pastikan middleware 'role' sudah terdaftar di Kernel
        ->group(function () {
            // Dashboard
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Resources (CRUD)
            Route::resources([
                'users'     => UserController::class,
                'courses'   => CourseController::class,
                'locations' => LocationController::class,
            ]);

            // Reports (Admin)
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [ReportController::class, 'index'])->name('index');
                Route::get('/{session}', [ReportController::class, 'show'])->name('show');
                Route::get('/export/excel', [ReportController::class, 'export'])->name('export');
            });
        });


    // 2. GROUP: LECTURER (Role: lecturer)
    Route::prefix('dosen')
        ->name('lecturer.')
        ->middleware('role:lecturer')
        ->group(function () {
            Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');
            // Menu Kelas untuk Dosen
            Route::resource('sessions', LecturerSessionController::class)->only(['index', 'create', 'store', 'show']);
        });


    // 3. GROUP: STUDENT (Role: student)
    Route::prefix('mahasiswa')
        ->name('student.')
        ->middleware('role:student')
        ->group(function () {
            // Dashboard Mahasiswa
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

            // --- FITUR ABSENSI (Attendance) ---
            // Route ini menggunakan StudentAttendanceController yang baru kita buat.
            // Awalan URL: /mahasiswa/absensi
            // Awalan Nama Route: student.attendance.
            Route::prefix('absensi')->name('attendance.')->group(function () {
                // 1. Halaman daftar sesi aktif (GET /mahasiswa/absensi)
                // Nama route: student.attendance.index
                Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');

                // 2. Proses Check-in/Hadir (POST /mahasiswa/absensi/{session}/check-in)
                // Nama route: student.attendance.store
                Route::post('/{session}/check-in', [StudentAttendanceController::class, 'store'])->name('store');
            });
        });

    Route::middleware(['auth', 'role:lecturer'])->prefix('dosen')->name('lecturer.')->group(function () {
        Route::resource('courses.sessions', \App\Http\Controllers\LecturerSessionController::class)->shallow();
    });
});
