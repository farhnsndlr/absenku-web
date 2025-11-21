<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Memproses data login yang dikirim form.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        // Pastikan email dan password diisi dan format email benar.
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Login (Attempt)
        // Auth::attempt() akan otomatis:
        // - Mencari user berdasarkan email.
        // - Meng-hash password input dan mencocokkannya dengan hash di DB.
        // - Jika cocok, membuat session login.
        // Parameter kedua 'true' adalah untuk fitur "Remember Me".
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Jika login SUKSES:

            // Regenerasi session ID untuk keamanan (mencegah session fixation attack)
            $request->session()->regenerate();

            // Ambil data user yang baru saja login
            $user = Auth::user();

            // 3. Logika Redirect Berdasarkan Role
            // Arahkan user ke dashboard yang sesuai dengan perannya.
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                    break;
                case 'lecturer':
                    return redirect()->route('lecturer.dashboard');
                    break;
                case 'student':
                    return redirect()->route('student.dashboard');
                    break;
                default:
                    // Fallback jika role tidak dikenali (seharusnya tidak terjadi)
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Role pengguna tidak valid.',
                    ]);
            }
        }

        // 4. Jika Login GAGAL:
        // Kembalikan ke halaman login dengan pesan error pada field 'email'.
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'), // Pesan error default Laravel ("Kredensial tidak cocok")
        ]);
    }
}
