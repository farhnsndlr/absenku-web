<?php

namespace App\Http\Controllers;

use App\Models\User; // Jangan lupa import Model User
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
     * --- BAGIAN INI YANG KEMARIN HILANG ---
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
            'password' => 'required|string|min:8|confirmed', // pastikan ada input password_confirmation di view
        ]);

        // 2. Simpan User ke Database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // Default role mahasiswa saat register mandiri
        ]);

        // 3. Langsung Login otomatis setelah daftar
        Auth::login($user);

        // 4. Redirect ke dashboard
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
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Role pengguna tidak valid.',
                    ]);
            }
        }

        // 4. Jika Login GAGAL
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
