<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Menampilkan form login.
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Menampilkan form registrasi.
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Menampilkan form lupa password.
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Mengirim tautan reset password ke email.
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Menampilkan form reset password.
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // Memproses reset password.
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Mendaftarkan akun baru.
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'npm' => 'required|string|max:255|unique:student_profiles,npm',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = null;
        DB::transaction(function () use ($request, &$user) {
            $profile = StudentProfile::create([
                'npm' => $request->npm,
                'class_name' => null,
                'full_name' => $request->name,
                'phone_number' => $request->phone,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'profile_id' => $profile->id,
                'profile_type' => StudentProfile::class,
            ]);
        });

        Auth::login($user);

        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('status', 'Kami sudah mengirim tautan verifikasi ke email Anda.');
    }

    // Memproses login pengguna.
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $this->invalidateOtherSessions($request, $user);
            Auth::logoutOtherDevices($request->password);

            if ($user->role === 'student' && !$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('status', 'Silakan verifikasi email Anda terlebih dahulu.');
            }

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
                        'email' => 'Role pengguna tidak valid atau belum diatur.',
                    ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    // Logout pengguna saat ini.
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    private function invalidateOtherSessions(Request $request, User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();
    }
}
