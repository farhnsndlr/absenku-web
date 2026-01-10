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
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    // Menampilkan daftar pengguna dengan filter.
    public function index(Request $request)
    {
        $query = User::with('profile')->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    // Menampilkan form tambah pengguna.
    public function create()
    {
        return view('admin.users.create');
    }

    // Menyimpan akun pengguna baru.
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            $profile = null;

            if ($validated['role'] === 'lecturer') {
                $profile = LecturerProfile::create([
                    'nid' => $validated['nid'],
                    'phone_number' => $validated['phone_number'] ?? null,
                    'full_name' => $validated['name'],
                ]);
            } elseif ($validated['role'] === 'student') {
                $profile = StudentProfile::create([
                    'npm' => $validated['npm'],
                    'class_name' => $validated['class_name'] ?? null,
                    'phone_number' => $validated['phone_number'] ?? null,
                    'full_name' => $validated['name'],
                ]);
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'profile_id' => $profile ? $profile->id : null,
                'profile_type' => $profile ? get_class($profile) : null,
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    // Menampilkan form edit pengguna.
    public function edit(User $user)
    {
        $user->load('profile');
        return view('admin.users.edit', compact('user'));
    }

    // Memperbarui data pengguna.
    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $user, $request) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            if ($user->profile) {
                $profileData = [
                    'phone_number' => $validated['phone_number'] ?? null,
                ];

                if ($user->role === 'lecturer') {
                    $profileData['nid'] = $validated['nid'];
                } elseif ($user->role === 'student') {
                    $profileData['npm'] = $validated['npm'];
                    $profileData['class_name'] = $validated['class_name'] ?? null;
                }

                $user->profile->update($profileData);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // Menghapus akun pengguna.
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
