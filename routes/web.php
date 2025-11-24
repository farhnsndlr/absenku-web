<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LecturerDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;

// ====================================================
// RUTE PUBLIC - Landing Page
// ====================================================
// Jika halaman ini hanya menampilkan view statis tanpa data,
// Route::view() lebih ringkas.
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

    // --- Global Authenticated Routes ---

    // Logout (Sebaiknya dipindah ke Controller agar web.php bersih)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
        return redirect()->route('landing');
    });
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    // Menampilkan form edit profil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    // Memproses update profil (menggunakan PATCH)
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Menampilkan form ubah password
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    // Memproses update password
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');


    // --- Role Based Routes ---

    // 1. GROUP: ADMIN (Role: admin)
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            // Dashboard
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Resources (CRUD)
            Route::resources([
                'users'     => UserController::class,
                'courses'   => CourseController::class,
                'locations' => LocationController::class,
            ]);
        });


    // 2. GROUP: LECTURER (Role: lecturer)
    Route::prefix('dosen')
        ->name('lecturer.')
        ->middleware('role:lecturer')
        ->group(function () {
            Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');

            // Tambahkan route khusus dosen lainnya di sini, misal: manajemen sesi kelas
            // Route::resource('sessions', LecturerSessionController::class);
        });


    // 3. GROUP: STUDENT (Role: student)
    Route::prefix('mahasiswa')
        ->name('student.')
        ->middleware('role:student')
        ->group(function () {
            // GUNAKAN CONTROLLER, JANGAN CLOSURE FUNCTION
            // Dashboard biasanya butuh data (misal: jadwal hari ini, dll), jadi sebaiknya pakai controller.
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

            // Halaman Absensi
            Route::get('/attendance', [StudentDashboardController::class, 'attendanceForm'])->name('attendance');
            // Route::post('/attendance', [StudentDashboardController::class, 'submitAttendance'])->name('attendance.submit');
        });
});
