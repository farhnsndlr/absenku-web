<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('landing');
})->name('landing');

// ====================================================
// RUTE GUEST (Belum Login)
// ====================================================
Route::middleware('guest')->group(function () {
    // Menampilkan Form Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Memproses Login
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

// ====================================================
// RUTE AUTHENTICATED (Sudah Login)
// ====================================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');


    // ------------------------------------------------
    // GROUP: ADMIN (Role: admin)
    // ------------------------------------------------
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin') // <-- Panggil alias yang tadi didaftarkan
        ->group(function () {

            Route::get('/dashboard', function () {
                return "Halaman Admin Dashboard (Laravel 12)";
            })->name('dashboard');

            // CRUD Locations dll
        });


    // ------------------------------------------------
    // GROUP: LECTURER (Role: lecturer)
    // ------------------------------------------------
    Route::prefix('dosen')
        ->name('lecturer.')
        ->middleware('role:lecturer')
        ->group(function () {

            Route::get('/dashboard', function () {
                return "Halaman Dosen Dashboard (Laravel 12)";
            })->name('dashboard');

            // Manajemen Sesi dll
        });


    // ------------------------------------------------
    // GROUP: STUDENT (Role: student)
    // ------------------------------------------------
    Route::prefix('mahasiswa')
        ->name('student.')
        ->middleware('role:student')
        ->group(function () {

            Route::get('/dashboard', function () {
                return "Halaman Mahasiswa Dashboard (Laravel 12)";
            })->name('dashboard');

            // Halaman Absen dll
        });
});
