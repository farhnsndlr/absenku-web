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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LecturerSessionController;

// PUBLIC
Route::view('/', 'landing')->name('landing');

// GUEST
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// AUTH
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifikasi
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    // PROFILE
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'editPassword'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // ===================== ADMIN =====================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resources([
            'users'     => UserController::class,
            'courses'   => CourseController::class,
            'locations' => LocationController::class,
        ]);

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/{session}', [ReportController::class, 'show'])->name('show');
            Route::get('/export/excel', [ReportController::class, 'export'])->name('export');
        });
    });

    // ===================== LECTURER =====================
    Route::prefix('dosen')->name('lecturer.')->middleware('role:lecturer')->group(function () {
        Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');

        // ABSENSI DOSEN (SESSIONs)
        Route::resource('sessions', LecturerSessionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        // Reports dosen
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('reports/{session}', [\App\Http\Controllers\ReportController::class, 'show'])
            ->name('reports.show');

        Route::get('/reports/export', [\App\Http\Controllers\ReportController::class, 'export'])
            ->name('reports.export');
    });

    // ===================== STUDENT =====================
    Route::prefix('mahasiswa')->name('student.')->middleware('role:student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('absensi')->name('attendance.')->group(function () {
            Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');
            Route::post('/{session}/check-in', [StudentAttendanceController::class, 'store'])->name('store');
        });
    });
});
