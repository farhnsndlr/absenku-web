<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\StudentProfile;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 0. Pastikan user adalah mahasiswa
        if ($user->role !== 'student') {
             abort(403, 'Akses ditolak. Halaman ini hanya untuk mahasiswa.');
        }

        // Ambil profile mahasiswa via relasi di model User
        // Asumsi di model User ada method: public function studentProfile() { return $this->hasOne(StudentProfile::class); }
        $studentProfile = $user->studentProfile;

        if (!$studentProfile) {
            // Idealnya redirect ke halaman lengkapi profil, tapi abort dulu untuk sekarang
            abort(403, 'Profil mahasiswa tidak ditemukan. Silakan hubungi admin.');
        }

        // --- PERBAIKAN KRUSIAL DI SINI ---
        // Gunakan ID dari StudentProfile, BUKAN ID dari User
        $studentProfileId = $studentProfile->id;

        $todayStr = Carbon::today()->toDateString(); // Format Y-m-d

        // --- 1. DATA STATISTIK KEHADIRAN (Dioptimalkan menjadi 1 Query) ---
        // KODE LAMA YANG TIDAK EFISIEN (N+1 Query):
        // $attendanceQuery = AttendanceRecord::where('student_id', $studentId);
        // 'present' => (clone $attendanceQuery)->where('status', 'present')->count(), ... dll

        // KODE BARU (OPTIMAL): Menggunakan conditional aggregation
        $rawStats = AttendanceRecord::where('student_id', $studentProfileId)
            ->selectRaw('
                sum(status = "present") as present,
                sum(status = "late") as late,
                sum(status IN ("permit", "sick")) as permit,
                sum(status = "absent") as absent
            ')
            ->first();

        // Format hasil agar aman jika null (belum ada record sama sekali)
        // Menggunakan null coalescing operator (?? 0)
        $stats = [
            'present' => $rawStats->present ?? 0,
            'late'    => $rawStats->late ?? 0,
            'permit'  => $rawStats->permit ?? 0,
            'absent'  => $rawStats->absent ?? 0,
            // Total kehadiran (hadir + terlambat)
            'total_attendance' => ($rawStats->present ?? 0) + ($rawStats->late ?? 0),
        ];


        // --- 2. JADWAL KULIAH HARI INI ---
        // a. Ambil ID mata kuliah yang diambil mahasiswa (dari object profile)
        $enrolledCourseIds = $studentProfile->courses()->pluck('courses.id');

        // b. Cari SESI yang aktif hari ini sebagai proxy jadwal.
        $todaysSchedule = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
            ->whereDate('session_date', $todayStr) // Gunakan whereDate agar lebih aman
            ->with([
                'course.lecturer.profile', // Load profile dosen agar dapat nama lengkap
                'location',
                // Eager load record absensi HANYA untuk mahasiswa ini menggunakan ID Profile yg benar
                'records' => function ($q) use ($studentProfileId) {
                    $q->where('student_id', $studentProfileId);
                }
            ])
            ->orderBy('start_time', 'asc')
            ->get();


        // --- 3. RIWAYAT ABSENSI TERAKHIR (5 Data) ---
        $recentHistory = AttendanceRecord::where('student_id', $studentProfileId) // Gunakan ID Profile
            ->with(['session.course']) // Load data sesi dan mata kuliahnya
            ->orderBy('submission_time', 'desc')
            ->take(5) // Ambil 5 saja
            ->get();


        // Kirim semua data ke view
        // Variable $user tidak perlu dikirim via compact karena sudah bisa diakses via auth()->user() di blade
        return view('student.dashboard', compact('stats', 'todaysSchedule', 'recentHistory'));
    }
}
