<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
// Tambahkan namespace untuk Rule jika menggunakan validasi lanjutan
use Illuminate\Validation\Rule;

class SessionController extends Controller
{
    // Tampilkan daftar sesi untuk satu mata kuliah tertentu
    public function index($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Authorisasi: Pastikan dosen yang login adalah pemilik kursus ini
        // Asumsi: User model memiliki relasi/kolom 'profile_id'
        if ($course->lecturer_id !== Auth::user()->profile_id) {
            abort(403, 'Anda tidak memiliki akses ke mata kuliah ini.');
        }

        // Eager load 'attendances' untuk menghitung jumlah yang hadir di view (opsional tapi bagus)
        $sessions = $course->sessions()->latest()->get();

        return view('lecturer.sessions.index', compact('course', 'sessions'));
    }

    // Form create session
    public function create()
    {
        // Ambil hanya mata kuliah yang diajar oleh dosen ini
        $courses = Course::where('lecturer_id', Auth::user()->profile_id)->get();

        // Jika dosen tidak punya mata kuliah, mungkin redirect atau tampilkan pesan
        if ($courses->isEmpty()) {
            return redirect()->route('lecturer.dashboard')->with('error', 'Anda belum memiliki mata kuliah.');
        }

        $locations = Location::all();
        return view('lecturer.sessions.create', compact('courses', 'locations'));
    }

    // Simpan sesi presensi
    public function store(Request $request)
    {
        // Dapatkan ID dosen saat ini
        $lecturerProfileId = Auth::user()->profile_id;

        $request->validate([
            // CRITICAL FIX: Pastikan course_id valid DAN milik dosen yang sedang login.
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) use ($lecturerProfileId) {
                    return $query->where('lecturer_id', $lecturerProfileId);
                }),
            ],
            'session_date' => 'required|date|after_or_equal:today', // Sebaiknya tidak membuat sesi di masa lalu
            'start_time' => 'required|date_format:H:i', // Perjelas format waktu
            'end_time' => 'required|date_format:H:i|after:start_time',
            'learning_type' => 'required|in:online,offline',
            // Validasi location_id hanya jika offline
            'location_id' => 'required_if:learning_type,offline|nullable|exists:locations,id',
            'lateness_limit' => 'required|integer|min:0',
            // Sesuaikan nama field di form (misal 'topic') dengan di database ('description')
            'topic' => 'nullable|string|max:255',
        ], [
            'course_id.exists' => 'Mata kuliah tidak valid atau Anda tidak memiliki akses.',
        ]);

        // BUG FIX: Anda men-generate token dua kali di kode lama. Cukup sekali di sini.
        $token = strtoupper(Str::random(8)); // Gunakan huruf besar agar mudah dibaca, 8-10 karakter cukup.

        AttendanceSession::create([
            'course_id' => $request->course_id,
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'learning_type' => $request->learning_type,
            // Gunakan null coalescing operator atau ternary untuk lokasi
            'location_id' => $request->learning_type === 'offline' ? $request->location_id : null,
            'lateness_limit' => $request->lateness_limit,
            'session_token' => $token,
            // Mapping dari input 'topic' ke kolom database 'description'
            'description' => $request->topic,
        ]);

        // Redirect ke halaman index sesi mata kuliah tersebut agar bisa lihat tokennya
        return redirect()->route('lecturer.sessions.index', $request->course_id)
            ->with('success', "Sesi berhasil dibuat! Token: $token");
    }

    // ==========================================
    // BAGIAN MAHASISWA
    // ==========================================

    // Mahasiswa melakukan presensi (BERBASIS TOKEN)
    public function studentCheckIn(Request $request)
    {
        $request->validate([
            // Pastikan input token diubah ke huruf besar jika kita menyimpannya dalam huruf besar
            'token' => 'required|string',
        ]);

        $inputToken = strtoupper($request->token);
        $student = Auth::user(); // Asumsi user yang login adalah mahasiswa

        // 1. Cari sesi berdasarkan token
        $session = AttendanceSession::where('session_token', $inputToken)->with('course')->first();

        if (!$session) {
            return back()->with('error', 'Token sesi tidak valid atau sesi tidak ditemukan.');
        }

        // 2. CRITICAL CHECK: Apakah mahasiswa mengambil mata kuliah ini?
        // Asumsi: Di model User ada relasi 'enrolledCourses' (many-to-many) ke model Course
        // Jika tidak dicek, mahasiswa dari kelas lain bisa absen jika tahu tokennya.
        /*
        if (! $student->enrolledCourses()->where('course_id', $session->course_id)->exists()) {
             return back()->with('error', 'Anda tidak terdaftar pada mata kuliah ini.');
        }
        */

        // 3. CRITICAL CHECK: Cek Waktu Sesi (Apakah sesi sedang berlangsung?)
        $now = Carbon::now();
        // Gabungkan tanggal dan jam untuk mendapatkan Carbon object yang lengkap
        $sessionStart = Carbon::parse($session->session_date . ' ' . $session->start_time);
        $sessionEnd = Carbon::parse($session->session_date . ' ' . $session->end_time);

        if ($now->lessThan($sessionStart)) {
            return back()->with('error', 'Sesi presensi belum dimulai.');
        }

        if ($now->greaterThan($sessionEnd)) {
            return back()->with('error', 'Sesi presensi sudah berakhir.');
        }

        // 4. CRITICAL CHECK: Apakah mahasiswa sudah pernah absen sebelumnya di sesi ini?
        // Mencegah double submit
        // Asumsi: relasi di model AttendanceSession bernama 'attendances'
        $alreadyCheckedIn = $session->attendances()
            ->where('student_id', Auth::user()->profile_id)
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah melakukan presensi untuk sesi ini.');
        }

        // 5. Hitung Keterlambatan
        $diffMinutes = $sessionStart->diffInMinutes($now, false);

        $status = 'on_time';
        if ($diffMinutes > $session->lateness_limit) {
            $status = 'late';
        }

        // 6. Simpan presensi
        $session->attendances()->create([
            'student_id' => Auth::user()->profile_id,
            'check_in_time' => $now,
            'status' => $status,
        ]);

        $message = $status === 'on_time' ? 'Presensi berhasil! Tepat waktu.' : 'Presensi berhasil, namun Anda tercatat terlambat.';

        return back()->with('success', $message);
    }
}
