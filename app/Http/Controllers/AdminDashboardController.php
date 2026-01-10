<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AdminAttendancePurged;

class AdminDashboardController extends Controller
{

    // Menyiapkan data untuk dashboard admin.
    public function index()
    {
        return view('admin.dashboard');
    }

    // Menghapus data presensi lama dengan konfirmasi.
    public function purgeAttendance(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Password admin wajib diisi.',
        ]);

        $user = Auth::user();
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors(['password' => 'Password admin tidak sesuai.'])
                ->withInput();
        }

        Artisan::call('attendance:purge', [
            '--all-finished' => true,
        ]);

        if ($user) {
            $user->notify(new AdminAttendancePurged());
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Data presensi selesai/terlewat berhasil dihapus.');
    }

    // Memverifikasi password admin untuk aksi sensitif.
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['valid' => false], 401);
        }

        return response()->json([
            'valid' => Hash::check($request->input('password'), $user->password),
        ]);
    }
}
