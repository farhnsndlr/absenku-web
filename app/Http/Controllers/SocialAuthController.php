<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    // Mengalihkan ke provider sosial.
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Menangani callback login sosial.
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        DB::transaction(function () use (&$user, $googleUser) {
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Mahasiswa',
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'role' => 'student',
                ]);
            } elseif (!$user->role) {
                $user->update([
                    'role' => 'student',
                ]);
            }

            if ($user->role === 'student' && !$user->studentProfile) {
                $profile = StudentProfile::create([
                    'npm' => null,
                    'full_name' => $googleUser->getName() ?: $user->name,
                ]);
                $user->update([
                    'profile_id' => $profile->id,
                    'profile_type' => StudentProfile::class,
                ]);
            }
        });

        Auth::login($user);
        if ($user->role === 'student') {
            return redirect()->route('profile.edit')
                ->with('success', 'Login Google berhasil. Lengkapi data profil Anda.');
        }

        return redirect()->route($this->redirectRouteFor($user))
            ->with('success', 'Login Google berhasil.');
    }

    private function redirectRouteFor(User $user): string
    {
        return match ($user->role) {
            'admin' => 'admin.dashboard',
            'lecturer' => 'lecturer.dashboard',
            default => 'student.dashboard',
        };
    }

}
