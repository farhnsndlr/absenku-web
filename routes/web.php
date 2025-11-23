<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Auth;

// ====================================================
// RUTE PUBLIC - Landing Page
// ====================================================
Route::get('/', function () {
    return view('landing');
})->name('landing');

// ====================================================
// RUTE GUEST (Belum Login)
// ====================================================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ====================================================
// RUTE AUTHENTICATED (Sudah Login)
// ====================================================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // ------------------------------------------------
    // GROUP: ADMIN (Role: admin)
    // ------------------------------------------------
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            // Dashboard Admin
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Resource Routes
            Route::resource('users', UserController::class);
            Route::resource('courses', CourseController::class);
            Route::resource('locations', LocationController::class);
        });

    // ------------------------------------------------
    // GROUP: LECTURER (Role: lecturer)
    // ------------------------------------------------
    Route::prefix('dosen')
        ->name('lecturer.')
        ->middleware('role:lecturer')
        ->group(function () {
            Route::get('/dashboard', function () {
                return view('lecturer.dashboard');
            })->name('dashboard');

            // Tambahkan route lecturer lainnya di sini
        });

    // ------------------------------------------------
    // GROUP: STUDENT (Role: student)
    // ------------------------------------------------
    Route::prefix('mahasiswa')
        ->name('student.')
        ->middleware('role:student')
        ->group(function () {
            Route::get('/dashboard', function () {
                return view('student.dashboard');
            })->name('dashboard');

            Route::get('/attendance', function () {
                return view('student.attendance.create');
            })->name('attendance');

            // Tambahkan route student lainnya di sini
        });
});
