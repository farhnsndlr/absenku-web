<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Location;
use App\Http\Requests\StoreSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class LecturerSessionController extends Controller
{
    /**
     * Menampilkan daftar sesi yang dibuat dosen ini.
     */
    public function index()
    {
        $lecturerId = Auth::id();

        // Ambil sesi yang course-nya diajar oleh dosen yang sedang login
        $sessions = AttendanceSession::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
            ->with(['course', 'location']) // Eager load relasi agar efisien
            ->orderBy('session_date', 'desc') // Urutkan dari yang terbaru
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        // Hitung total courses untuk dosen ini (untuk dashboard stats)
        $totalCourses = Course::where('lecturer_id', $lecturerId)->count();

        // Hitung total sessions (opsional, jika diperlukan di view)
        $totalSessions = AttendanceSession::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })->count();

        // Statistik tambahan yang mungkin diperlukan
        $upcomingSessions = AttendanceSession::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
            ->where('session_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->count();

        $completedSessions = AttendanceSession::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
            ->where('status', 'completed')
            ->count();

        return view('lecturer.sessions.index', compact(
            'sessions',
            'totalCourses',
            'totalSessions',
            'upcomingSessions',
            'completedSessions'
        ));
    }

    /**
     * Menampilkan form buat sesi baru.
     */
    public function create()
    {
        $lecturerId = Auth::id();

        // Ambil mata kuliah yang diajar dosen ini, lengkap dengan data lokasi default-nya
        $myCourses = Course::where('lecturer_id', $lecturerId)
            ->with('location') // Eager load lokasi default
            ->orderBy('course_name')
            ->get();

        // Ambil semua lokasi untuk dropdown pilihan (jika dosen ingin mengubah lokasi)
        $locations = Location::orderBy('location_name')->get();

        if ($myCourses->isEmpty()) {
            return redirect()->route('lecturer.dashboard')
                ->with('error', 'Anda belum memiliki mata kuliah yang diampu. Hubungi Admin.');
        }

        // Kita perlu mengirim data default mata kuliah dalam format JSON ke view
        // agar bisa dipakai oleh Alpine.js untuk mengisi form secara otomatis.
        $coursesDefaults = $myCourses->mapWithKeys(function ($course) {
            return [$course->id => [
                // Format waktu ke H:i agar sesuai dengan input type="time"
                'start_time' => $course->start_time ? Carbon::parse($course->start_time)->format('H:i') : null,
                'end_time' => $course->end_time ? Carbon::parse($course->end_time)->format('H:i') : null,
                'session_type' => $course->session_type,
                'location_id' => $course->location_id,
            ]];
        });

        // Hitung total courses untuk dashboard (jika view extends dashboard)
        $totalCourses = $myCourses->count();

        return view('lecturer.sessions.create', compact('myCourses', 'locations', 'coursesDefaults', 'totalCourses'));
    }

    /**
     * Menyimpan sesi baru.
     */
    public function store(StoreSessionRequest $request)
    {
        $validated = $request->validated();

        // Generate token unik 6 karakter (huruf besar)
        $validated['session_token'] = Str::upper(Str::random(6));

        $validated['lecturer_id'] = Auth::id();

        // Status awal 'scheduled'
        $validated['status'] = 'scheduled';

        // Jika sesi online, pastikan location_id null meskipun ada inputan (untuk keamanan)
        if ($validated['session_type'] === 'online') {
            $validated['location_id'] = null;
        }

        AttendanceSession::create($validated);

        return redirect()->route('lecturer.sessions.index')
            ->with('success', 'Sesi kelas baru berhasil dijadwalkan.');
    }

    /**
     * Menampilkan detail sesi (nanti untuk rekap absen).
     */
    public function show(AttendanceSession $session)
    {
        // Pastikan dosen hanya bisa melihat sesi dari mata kuliah yang diajarnya
        if ($session->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load relasi yang dibutuhkan
        $session->load(['course', 'location', 'attendanceRecords.student.studentProfile']);

        // Hitung statistik untuk sesi ini
        $totalStudents = $session->attendanceRecords->count();
        $presentCount = $session->attendanceRecords->where('status', 'present')->count();
        $absentCount = $session->attendanceRecords->where('status', 'absent')->count();
        $sickCount = $session->attendanceRecords->where('status', 'sick')->count();

        $attendanceRate = $totalStudents > 0
            ? round(($presentCount / $totalStudents) * 100, 2)
            : 0;

        // Total courses untuk dashboard layout
        $totalCourses = Course::where('lecturer_id', Auth::id())->count();

        return view('lecturer.sessions.show', compact(
            'session',
            'totalStudents',
            'presentCount',
            'absentCount',
            'sickCount',
            'attendanceRate',
            'totalCourses'
        ));
    }

    public function edit($id)
    {
        $session = AttendanceSession::with(['course', 'location'])
            ->findOrFail($id);

        $locations = Location::all(); // jika ingin bisa ganti lokasi
        $courses = Course::where('lecturer_id', Auth::user()->id)->get();


        return view('lecturer.sessions.edit', compact('session', 'locations', 'courses'));
    }

    public function update(Request $request, $id)
    {
        // 1. Ambil data sesi beserta coursenya untuk cek otorisasi
        $session = AttendanceSession::with('course')->findOrFail($id);

        // 2. Cek Otorisasi: Pastikan ini sesi milik dosen yang login
        if ($session->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 3. Lakukan Validasi dengan nama field YANG BENAR
        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'class_name' => ['required', 'string', 'max:50'],
            'learning_type' => ['required', 'in:online,offline'],
            // -------------------------------

            // Validasi lokasi: wajib hanya jika tipenya offline
            'location_id' => [
                'required_if:session_type,offline',
                'nullable',
                Rule::exists('locations', 'id'),
            ],
            'topic' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,open,closed'],
        ]);

        // 4. Logika bisnis: Jika diubah jadi online, paksa lokasi jadi null
        if ($validated['learning_type'] === 'online') {
            $validated['location_id'] = null;
        }

        // 5. Update data dengan data yang sudah divalidasi
        $session->update($validated);

        // 6. Redirect kembali dengan pesan sukses
        return redirect()->route('lecturer.sessions.show', $session->id)
            ->with('success', 'Sesi presensi berhasil diperbarui.');
    }

    public function destroy(AttendanceSession $session)
    {
        // Pastikan dosen hanya bisa menghapus sesi miliknya sendiri
        if ($session->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah sesi sudah ada yang absen. Jika ada, mungkin tidak boleh dihapus.
        if ($session->attendanceRecords()->exists()) {
            return back()->with('error', 'Sesi ini tidak bisa dihapus karena sudah ada mahasiswa yang melakukan absensi.');
        }

        $session->delete();
        return redirect()->route('lecturer.sessions.index')
            ->with('success', 'Sesi kelas berhasil dihapus.');
    }
}
