<?php

use Illuminate\Support\Facades\Route;

// Import Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;

// Admin Controllers
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LocationController;

// Lecturer Controllers
use App\Http\Controllers\LecturerDashboardController;
use App\Http\Controllers\LecturerSessionController;

// Student Controllers
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentAttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================================================================
// PUBLIC ROUTES
// ========================================================================
Route::view('/', 'landing')->name('landing');

// ========================================================================
// GUEST ROUTES (Belum Login)
// ========================================================================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ========================================================================
// AUTHENTICATED ROUTES (Sudah Login - Semua Role)
// ========================================================================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifications
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'editPassword'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // ====================================================================
    // ROLE: ADMIN
    // ====================================================================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Master Data Resources
        Route::resources([
            'users'     => UserController::class,
            'courses'   => CourseController::class,
            'locations' => LocationController::class,
        ]);

        // Reports (Admin View)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            // Route spesifik (export) HARUS ditaruh sebelum route wildcard (show)
            Route::get('/export/excel', [ReportController::class, 'export'])->name('export');
            Route::get('/{session}', [ReportController::class, 'show'])->name('show');
        });
    });

    // ====================================================================
    // ROLE: LECTURER (Dosen)
    // ====================================================================
    Route::prefix('dosen')->name('lecturer.')->middleware('role:lecturer')->group(function () {
        // Dashboard
        Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');

        // Session Management (Absensi)
        Route::resource('sessions', LecturerSessionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Reports (Lecturer View)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
            Route::get('/{session}', [ReportController::class, 'show'])->name('show');
        });
    });

    // ====================================================================
    // ROLE: STUDENT (Mahasiswa)
    // ====================================================================
    Route::prefix('mahasiswa')->name('student.')->middleware('role:student')->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Route untuk menampilkan form input token
        Route::get('/presensi/input', [StudentAttendanceController::class, 'showTokenForm'])->name('attendance.input');

        // Route untuk memproses submit token (POST)
        Route::post('/presensi/process', [StudentAttendanceController::class, 'processToken'])->name('attendance.process');

        // Attendance Check-in
        Route::prefix('absensi')->name('attendance.')->group(function () {
            Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');
            Route::post('/{session}/check-in', [StudentAttendanceController::class, 'store'])->name('store');
        });

        Route::post('/{session}/permission', [StudentAttendanceController::class, 'permission'])
            ->name('permission');

        Route::get('/student/attendance/{sessionId}/checkin', [StudentAttendanceController::class, 'checkinForm'])->name('student.checkin.form');
        Route::post('/student/attendance/{sessionId}/checkin', [StudentAttendanceController::class, 'submitCheckin'])->name('student.checkin.submit');
    });
});
