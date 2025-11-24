<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// Import Model yang dibutuhkan
use App\Models\StudentProfile;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\AttendanceSession;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 0. Pastikan user adalah mahasiswa dan punya profil
        if ($user->role !== 'student' || !($user->profile instanceof StudentProfile)) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk mahasiswa.');
        }
        $studentId = $user->id;
        $today = Carbon::today();

        // --- 1. DATA STATISTIK KEHADIRAN (Semester Ini) ---
        // Kita ambil semua record kehadiran mahasiswa ini
        $attendanceQuery = AttendanceRecord::where('student_id', $studentId);
        // (Opsional: Filter berdasarkan semester aktif jika ada logika semesternya)

        $stats = [
            // Menghitung jumlah per status
            'present' => (clone $attendanceQuery)->where('status', 'present')->count(),
            'late'    => (clone $attendanceQuery)->where('status', 'late')->count(),
            'permit'  => (clone $attendanceQuery)->whereIn('status', ['permit', 'sick'])->count(),
            'absent'  => (clone $attendanceQuery)->where('status', 'absent')->count(),
        ];
        // Total kehadiran (hadir + terlambat)
        $stats['total_attendance'] = $stats['present'] + $stats['late'];


        // --- 2. JADWAL KULIAH HARI INI ---
        // a. Ambil ID mata kuliah yang diambil mahasiswa
        $enrolledCourseIds = $user->profile->courses()->pluck('courses.id');

        // b. Cari Course yang jadwalnya HARI INI
        // Asumsi: ada kolom 'schedule_day' (e.g., 'Senin') di tabel courses.
        // Jika belum ada, kita pakai cara manual dulu dengan mencari Sesi hari ini.
        // Cara yang lebih baik adalah menambahkan kolom hari dan jam di tabel courses.

        // SEMENTARA: Kita cari SESI yang aktif hari ini sebagai proxy jadwal.
        // Ini tidak ideal, tapi cukup untuk MVP.
        // Nanti sebaiknya tabel 'courses' punya kolom 'day_of_week' dan 'start_time'.
        $todaysSchedule = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
            ->where('session_date', $today)
            ->with(['course.lecturer', 'location', 'records' => function ($q) use ($studentId) {
                // Eager load record absensi HANYA untuk mahasiswa ini
                $q->where('student_id', $studentId);
            }])
            ->orderBy('start_time', 'asc')
            ->get();


        // --- 3. RIWAYAT ABSENSI TERAKHIR (5 Data) ---
        $recentHistory = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course']) // Load data sesi dan mata kuliahnya
            ->orderBy('submission_time', 'desc')
            ->take(5) // Ambil 5 saja
            ->get();


        // Kirim semua data ke view
        return view('student.dashboard', compact('user', 'stats', 'todaysSchedule', 'recentHistory'));
    }
}
