<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// Import Model yang dibutuhkan
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\StudentProfile;

class StudentAttendanceController extends Controller
{
    /**
     * Menampilkan daftar sesi absensi yang tersedia untuk mahasiswa.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Pastikan user adalah mahasiswa dan punya profil
        if ($user->role !== 'student' || !($user->profile instanceof StudentProfile)) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk mahasiswa.');
        }

        // 2. Ambil ID semua mata kuliah yang diambil mahasiswa ini
        // Kita gunakan relasi 'courses' yang ada di model StudentProfile
        $enrolledCourseIds = $user->profile->courses()->pluck('courses.id');

        $now = Carbon::now(); // Waktu sekarang

        // 3. Cari Sesi Absensi yang memenuhi kriteria:
        $activeSessions = AttendanceSession::whereIn('course_id', $enrolledCourseIds) // a. Mata kuliahnya diambil mahasiswa
            ->where('session_date', Carbon::today()) // b. Sesi untuk HARI INI
            ->where('start_time', '<=', $now) // c. Waktu mulai sudah lewat atau sekarang
            ->where('end_time', '>=', $now)   // d. Waktu selesai belum lewat
            // e. PENTING: Filter sesi yang BELUM dihadiri oleh mahasiswa ini.
            //    Kita gunakan whereDoesntHave untuk mengecek apakah TIDAK ADA
            //    record di tabel attendance_records untuk sesi ini DAN mahasiswa ini.
            ->whereDoesntHave('records', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->with(['course', 'location']) // Eager load data terkait untuk tampilan
            ->orderBy('start_time', 'asc')
            ->get();

        // 4. Tampilkan view dengan data sesi aktif
        // Kita akan buat view ini nanti: resources/views/student/attendance/index.blade.php
        return view('student.attendance.index', compact('user', 'activeSessions'));
    }

    /**
     * Memproses check-in (kehadiran) mahasiswa.
     * Menerima ID sesi sebagai parameter route.
     */
    public function store(Request $request, $sessionId)
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 1. Pastikan user punya profil mahasiswa (karena kita butuh ID-nya)
        if (!$user->studentProfile) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan. Hubungi admin.');
        }
        $studentProfileId = $user->studentProfile->id;

        // 2. Cari Sesi
        $session = AttendanceSession::findOrFail($sessionId);

        // --- VALIDASI (Waktu, Enrollment) ---
        // (Kode validasi waktu dan enrollment di sini sama seperti sebelumnya)
        // ...

        // 3. Cek Double Submit (Gunakan nama kolom baru)
        $alreadyCheckedIn = AttendanceRecord::where('session_id', $session->id)
            ->where('student_id', $studentProfileId) // <-- ID StudentProfile
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah tercatat hadir untuk sesi ini.');
        }

        // --- PROSES SIMPAN (Sesuai Model Lama) ---

        // 4. Buat Record Kehadiran Baru
        AttendanceRecord::create([
            'session_id' => $session->id,
            'student_id' => $studentProfileId, // <-- ID StudentProfile
            'status' => 'present', // Status otomatis 'hadir'
            'submission_time' => $now,
            'learning_type' => $session->session_type, // Warisi tipe dari sesi (online/onsite)
            // 'photo_path' => ..., // Nanti diisi dari upload file
            // 'location_maps' => ..., // Nanti diisi dari data geolokasi
        ]);

        // 5. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('student.attendance.index')->with('success', 'Berhasil melakukan absensi! Kehadiran Anda telah tercatat.');
    }
}
