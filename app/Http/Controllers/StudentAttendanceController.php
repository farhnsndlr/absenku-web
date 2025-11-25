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

        if ($user->role !== 'student' || !($user->profile instanceof StudentProfile)) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk mahasiswa.');
        }

        $enrolledCourseIds = $user->profile->courses()->pluck('courses.id');
        $now = Carbon::now();

        // AUTO UPDATE: Tutup sesi yg sudah lewat end_time
        AttendanceSession::where('end_time', '<', $now)
            ->where('status', 'open') // pastikan tidak mengupdate berulang
            ->update(['status' => 'closed']);

        // Ambil sesi yang masih aktif
        $activeSessions = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
            ->where('session_date', Carbon::today())
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->where('status', 'open')
            ->whereDoesntHave('records', function ($query) use ($user) {
                $query->where('student_id', $user->studentProfile->id);
            })
            ->with(['course', 'location'])
            ->orderBy('start_time', 'asc')
            ->get();

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

        if (!$user->studentProfile) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan. Hubungi admin.');
        }
        $studentProfileId = $user->studentProfile->id;

        $session = AttendanceSession::findOrFail($sessionId);

        // CEK wajib terdaftar pada mata kuliah
        if (!$user->profile->courses()->where('courses.id', $session->course_id)->exists()) {
            return back()->with('error', 'Anda tidak terdaftar di mata kuliah ini.');
        }

        // CEK apakah waktu sudah melewati end_time → otomatis close
        if ($now->greaterThan(Carbon::parse($session->end_time))) {
            if ($session->status !== 'closed') {
                $session->update(['status' => 'closed']);
            }
            return back()->with('error', 'Sesi absensi telah ditutup. Kamu tidak dapat melakukan presensi lagi.');
        }

        // Jika status sudah closed dari sebelumya
        if ($session->status === 'closed') {
            return back()->with('error', 'Sesi absensi telah ditutup.');
        }

        // Cegah double check-in
        $alreadyCheckedIn = AttendanceRecord::where('session_id', $session->id)
            ->where('student_id', $studentProfileId)
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah tercatat hadir untuk sesi ini.');
        }

        // SIMPAN ABSENSI
        AttendanceRecord::create([
            'session_id' => $session->id,
            'student_id' => $studentProfileId,
            'status' => 'present',
            'submission_time' => $now,
            'learning_type' => $session->session_type,
        ]);

        return redirect()->route('student.attendance.index')->with('success', 'Berhasil melakukan absensi! Kehadiran Anda telah tercatat.');
    }
}
