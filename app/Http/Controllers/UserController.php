<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
// Import Request Validation yang sudah dibuat
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     */
    public function index(Request $request)
    {
        // Mulai query
        $query = User::with('profile')->latest();

        // Filter berdasarkan Role jika ada di request
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter pencarian (nama atau email) jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Paginate hasil akhir (10 per halaman)
        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form tambah pengguna baru.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    // Gunakan UserStoreRequest untuk validasi otomatis
    public function store(UserStoreRequest $request)
    {
        // Data yang sudah divalidasi
        $validated = $request->validated();

        // Gunakan Transaksi Database untuk keamanan data (karena simpan ke 2 tabel)
        DB::transaction(function () use ($validated) {

            // 1. Buat Data Profil Terlebih Dahulu (Jika bukan admin)
            $profile = null;

            if ($validated['role'] === 'lecturer') {
                // Buat profil dosen
                $profile = LecturerProfile::create([
                    'nid' => $validated['nid'],
                    'phone_number' => $validated['phone_number'] ?? null,
                    'full_name' => $validated['name'],
                ]);
            } elseif ($validated['role'] === 'student') {
                // Buat profil mahasiswa
                $profile = StudentProfile::create([
                    'npm' => $validated['npm'],
                    'phone_number' => $validated['phone_number'] ?? null,
                    'full_name' => $validated['name'],
                ]);
            }

            // 2. Buat Data User Akun
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), // Jangan lupa di-hash
                'role' => $validated['role'],
                // Hubungkan profil jika ada (relasi polimorfik)
                'profile_id' => $profile ? $profile->id : null,
                'profile_type' => $profile ? get_class($profile) : null,
            ]);
        }); // Akhir transaksi

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit pengguna.
     * Laravel otomatis mencari User berdasarkan ID di URL (Route Model Binding)
     */
    public function edit(User $user)
    {
        // Eager load profile agar datanya siap di view
        $user->load('profile');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna.
     */
    // PASTIKAN SIGNATURE INI BENAR: Ada $request DAN $user
    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $user, $request) {
            // 1. Update Data User Akun
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            // Cek apakah password diisi di form. Jika ya, update password.
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            // 2. Update Data Profil (Jika ada)
            // Kita cek apakah user ini punya profil (admin tidak punya)
            if ($user->profile) {
                $profileData = [
                    'phone_number' => $validated['phone_number'] ?? null,
                ];

                // Tambahkan NID/NPM tergantung role untuk diupdate
                if ($user->role === 'lecturer') {
                    $profileData['nid'] = $validated['nid'];
                } elseif ($user->role === 'student') {
                    $profileData['npm'] = $validated['npm'];
                }

                // Update record di tabel profil terkait
                $user->profile->update($profileData);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     **/
    public function destroy(User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Logika delete
        // Karena kita pakai 'onDelete("cascade")' di migrasi profil,
        // menghapus user otomatis menghapus profilnya di tabel lain. Aman.
        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
