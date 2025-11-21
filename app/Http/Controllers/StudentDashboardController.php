<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $profile = $student->profile; // StudentProfile

        // 1. Cari Sesi Aktif HARI INI
        // Logic: Tanggal hari ini DAN (Waktu sekarang ada di antara start & end)
        $now = Carbon::now();

        // Ambil ID semua course yang diambil mahasiswa ini
        // (Pastikan relasi many-to-many di model StudentProfile sudah benar)
        $enrolledCourseIds = $profile->courses->pluck('id');

        $activeSessions = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
            ->whereDate('session_date', $now->toDateString())
            ->whereTime('start_time', '<=', $now->toTimeString())
            ->whereTime('end_time', '>=', $now->toTimeString())
            ->with(['course', 'location'])
            ->get();

        // 2. Hitung Statistik Sederhana (Hadir vs Alpha)
        // (Bisa dikembangkan nanti)
        $stats = [
            'present' => $profile->attendanceRecords()->where('status', 'present')->count(),
            'total' => $profile->attendanceRecords()->count(),
        ];

        return view('student.dashboard', compact('activeSessions', 'stats'));
    }
}
