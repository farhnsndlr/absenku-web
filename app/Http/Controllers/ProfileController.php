<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function show()
    {
        // 1. Ambil user yang sedang login
        $user = Auth::user();

        // 2. Eager load relasi 'profile' agar efisien
        // Laravel otomatis tahu harus mengambil StudentProfile atau LecturerProfile
        $user->load('profile');

        // 3. Siapkan data tambahan berdasarkan peran (role)
        $additionalData = [];

        if ($user->role === 'lecturer' && $user->profile instanceof LecturerProfile) {
            // Jika Dosen, ambil data mata kuliah yang diampu
            $additionalData['courses_taught'] = $user->profile->courses()->get();
        } elseif ($user->role === 'student' && $user->profile instanceof StudentProfile) {
            // Jika Mahasiswa, ambil data mata kuliah yang diambil (enroll)
            // Menggunakan relasi belongsToMany 'courses' di model StudentProfile
            $additionalData['courses_enrolled'] = $user->profile->courses()->get();
        }

        // 4. Kirim data user dan data tambahan ke view
        return view('profile.show', compact('user', 'additionalData'));
    }
}
