<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LecturerDashboardController;
use App\Http\Controllers\LecturerSessionController;
use App\Http\Controllers\AttendanceMediaController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentAttendanceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Publik
Route::view('/', 'landing')->name('landing');

// Tamu
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/auth/google', [SocialAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'callback']);
});

// Terautentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        $user = $request->user();
        $redirect = match ($user->role) {
            'admin' => 'admin.dashboard',
            'lecturer' => 'lecturer.dashboard',
            default => 'student.dashboard',
        };

        return redirect()->route($redirect)
            ->with('success', 'Email berhasil diverifikasi.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Tautan verifikasi baru telah dikirim.');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])
        ->name('notifications.unread');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])
        ->name('notifications.markRead');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'editPassword'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // Admin
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/attendance/purge', [AdminDashboardController::class, 'purgeAttendance'])
            ->name('attendance.purge');
        Route::post('/attendance/verify-password', [AdminDashboardController::class, 'verifyPassword'])
            ->name('attendance.verify-password');

        Route::resources([
            'users'     => UserController::class,
            'courses'   => CourseController::class,
            'locations' => LocationController::class,
        ]);

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export/excel', [ReportController::class, 'export'])->name('export');
            Route::get('/{session}', [ReportController::class, 'show'])->name('show');
        });
    });

    // Dosen
    Route::prefix('dosen')->name('lecturer.')->middleware('role:lecturer')->group(function () {
        Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');

        Route::resource('sessions', LecturerSessionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
            Route::get('/{session}', [ReportController::class, 'show'])->name('show');
        });
    });

    // Mahasiswa
    Route::prefix('mahasiswa')->name('student.')->middleware(['role:student', 'verified'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/presensi/input', [StudentAttendanceController::class, 'showTokenForm'])->name('attendance.input');
        Route::post('/presensi/process', [StudentAttendanceController::class, 'processToken'])->name('attendance.process');

        Route::prefix('absensi')->name('attendance.')->group(function () {
            Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');
            Route::post('/{session}/check-in', [StudentAttendanceController::class, 'store'])->name('store');
        });

        Route::post('/{session}/permission', [StudentAttendanceController::class, 'permission'])
            ->name('permission');

        Route::get('/student/attendance/{sessionId}/checkin', [StudentAttendanceController::class, 'checkinForm'])->name('student.checkin.form');
        Route::post('/student/attendance/{sessionId}/checkin', [StudentAttendanceController::class, 'submitCheckin'])->name('student.checkin.submit');
    });

    Route::get('/media/attendance/{path}', [AttendanceMediaController::class, 'show'])
        ->where('path', '.*')
        ->name('attendance.media');
});
