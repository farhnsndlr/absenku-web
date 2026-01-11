<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    // Mengalihkan ke provider sosial.
    public function redirect(Request $request)
    {
        $intent = $request->query('intent', 'login');
        $request->session()->put('auth_intent', $intent);

        return Socialite::driver('google')->redirect();
    }

    // Menangani callback login sosial.
    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        $intent = session()->pull('auth_intent', 'login');

        $createdNew = false;

        DB::transaction(function () use (&$user, $googleUser, &$createdNew) {
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Mahasiswa',
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'role' => 'student',
                ]);
                $createdNew = true;
            } elseif (!$user->role) {
                $user->update([
                    'role' => 'student',
                ]);
            }

            if (!$user->email_verified_at) {
                $user->forceFill([
                    'email_verified_at' => now(),
                ])->save();
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
        $request->session()->regenerate();
        $this->invalidateOtherSessions($request, $user);
        if ($intent === 'signup' || $createdNew) {
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
