<?php

namespace App\Http\Controllers;

use App\Models\User; // Import Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash untuk password
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menampilkan form register.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran user baru (Register).
     */
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // Pastikan di view register ada input dengan name="password_confirmation"
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Simpan User ke Database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Default role mahasiswa saat register mandiri.
            // Jika ingin dosen/admin mendaftar, sebaiknya lewat fitur kelola user di dashboard admin, bukan register publik ini.
            'role' => 'student',
        ]);

        // 3. Langsung Login otomatis setelah daftar
        Auth::login($user);

        // 4. Redirect ke dashboard student
        // Menggunakan helper route() lebih aman daripada hardcode URL
        return redirect()->route('student.dashboard');
    }

    /**
     * Memproses data login yang dikirim form.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Login (Attempt)
        // Parameter kedua true/false berdasarkan input 'remember' (checkbox "Ingat Saya")
        if (Auth::attempt($credentials, $request->filled('remember'))) {

            $request->session()->regenerate();
            $user = Auth::user();

            // 3. Logika Redirect Berdasarkan Role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'lecturer':
                    return redirect()->route('lecturer.dashboard');
                case 'student':
                    return redirect()->route('student.dashboard');
                default:
                    // Jika role tidak dikenali, logout paksa demi keamanan
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Role pengguna tidak valid atau belum diatur.',
                    ]);
            }
        }

        // 4. Jika Login GAGAL
        throw ValidationException::withMessages([
            // trans('auth.failed') mengambil pesan dari file bahasa resources/lang/en/auth.php
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Logout user.
     */
    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        // Cek status SEBELUM logout

        // Proses Logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Cek status SESUDAH logout dan matikan proses

        // Baris ini tidak akan dieksekusi karena ada dd() di atasnya
        return redirect()->route('landing');
    }
}
