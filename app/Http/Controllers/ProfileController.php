<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;

class ProfileController extends Controller
{


    /**
     * Menampilkan halaman profil pengguna (Method show yang lama).
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('profile');

        $additionalData = [];
        if ($user->role === 'lecturer' && $user->profile instanceof LecturerProfile) {
            $additionalData['courses_taught'] = $user->profile->courses()->get();
        } elseif ($user->role === 'student' && $user->profile instanceof StudentProfile) {
            $additionalData['courses_enrolled'] = $user->profile->courses()->get();
        }

        return view('profile.show', compact('user', 'additionalData'));
    }


    private function getDashboardView()
    {
        // Mapping antara role dan file view dashboard-nya
        $roleViews = [
            'admin' => 'admin.dashboard',
            'lecturer' => 'lecturer.dashboard',
            'student' => 'student.dashboard',
        ];

        $role = Auth::user()->role;

        // Kembalikan nama view yang sesuai role,
        // Jika role tidak dikenali, fallback ke layout utama 'layouts.dashboard'
        return $roleViews[$role] ?? 'layouts.dashboard';
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();

        // Kita kirim variabel $dashboardView ke view
        return view('profile.edit', [
            'user' => $user,
            'dashboardView' => $this->getDashboardView()
        ]);
    }

    /**
     * Memproses update profil.
     */
    public function update(ProfileUpdateRequest $request)
    {
        // Data sudah divalidasi oleh ProfileUpdateRequest
        $user = $request->user();
        // Ambil semua data yang sudah divalidasi
        $validatedData = $request->validated();

        // --- 1. Handle Upload Foto ---
        if ($request->hasFile('photo')) {
            // a. Hapus foto lama jika ada (agar storage tidak penuh)
            if ($user->profile_photo_path) {
                // Gunakan disk 'public' karena kita menyimpannya di sana
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // b. Simpan foto baru
            // store('profile-photos', 'public') akan menyimpan file di storage/app/public/profile-photos
            // dan mengembalikan path-nya (misal: profile-photos/namafileunik.jpg)
            $path = $request->file('photo')->store('profile-photos', 'public');

            // c. Masukkan path ke array data yang akan diupdate ke tabel users
            $validatedData['profile_photo_path'] = $path;
        }

        // --- 2. Update data di tabel users ---

        // Cek apakah email diubah. Jika ya, reset status verifikasi email.
        // PENTING: Pengecekan ini harus dilakukan SEBELUM data disimpan.
        // Kita bandingkan email baru dari request dengan email lama di user.
        if ($validatedData['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        // Lakukan update massal pada model User.
        // Kita hanya mengambil field yang relevan untuk tabel users.
        // Field 'photo', 'nid', 'npm', 'phone_number' akan diabaikan di sini karena tidak ada di tabel users.
        $user->update(\Illuminate\Support\Arr::only($validatedData, ['name', 'email', 'profile_photo_path']));


        // --- 3. Update data di tabel profil polimorfik (lecturer_profiles / student_profiles) ---

        // Ambil data yang relevan untuk profil tambahan
        // Kita gunakan array_filter untuk menghapus nilai null/kosong agar tidak menimpa data dengan null
        $profileData = array_filter($request->only(['phone_number', 'nid', 'npm']), function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Jika ada data profil yang dikirim, lakukan update melalui relasi polimorfik
        if (!empty($profileData)) {
            $user->profile()->update($profileData);
        }

        // Redirect kembali ke halaman show dengan pesan sukses
        return redirect()->route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Menampilkan form ubah password.
     */
    public function editPassword()
    {
        // Kita kirim variabel $dashboardView ke view
        return view('profile.password', [
            'dashboardView' => $this->getDashboardView()
        ]);
    }

    /**
     * Memproses update password.
     */
    public function updatePassword(PasswordUpdateRequest $request)
    {
        // Data sudah divalidasi. Password lama sudah dicek cocok.
        $user = $request->user();

        // Update password dengan yang baru dan di-hash
        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()->route('profile.show')->with('status', 'password-updated');
    }
}
