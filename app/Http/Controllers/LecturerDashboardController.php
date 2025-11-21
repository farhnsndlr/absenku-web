<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class LecturerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pastikan user punya profil dosen
        if (!$user->profile_id) {
            return back()->with('error', 'Profil Dosen tidak ditemukan.');
        }

        // Ambil mata kuliah milik dosen ini
        $courses = Course::where('lecturer_id', $user->profile_id)
            ->withCount('sessions') // Hitung jumlah sesi (opsional)
            ->get();

        return view('lecturer.dashboard', compact('courses'));
    }
}
