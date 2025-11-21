<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    // Tampilkan daftar sesi untuk mata kuliah tertentu
    public function index($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Validasi: Dosen yang login harus pemilik course ini
        if ($course->lecturer_id !== Auth::user()->profile_id) {
            abort(403, 'Unauthorized action.');
        }

        $sessions = $course->sessions()->latest()->get();

        return view('lecturer.sessions.index', compact('course', 'sessions'));
    }

    // Tampilkan form buat sesi baru
    public function create()
    {
        // Kita butuh daftar mata kuliah dosen ini untuk dropdown (opsional jika create dari halaman detail)
        $courses = Course::where('lecturer_id', Auth::user()->profile_id)->get();

        // Kita butuh daftar lokasi untuk dropdown 'Onsite'
        $locations = Location::all();

        return view('lecturer.sessions.create', compact('courses', 'locations'));
    }

    // Simpan sesi baru
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'session_date' => 'required|date',
            'start_time' => 'required', // format H:i
            'end_time' => 'required|after:start_time',
            'session_type' => 'required|in:online,onsite',
            'location_id' => 'required_if:session_type,onsite', // Wajib jika Onsite
            'description' => 'nullable|string'
        ]);

        // Gabungkan date + time agar jadi timestamp penuh (sesuai struktur DB)
        // Catatan: Jika di DB tipe 'timestamp', Laravel butuh format 'Y-m-d H:i:s'
        $startDateTime = $request->session_date . ' ' . $request->start_time;
        $endDateTime = $request->session_date . ' ' . $request->end_time;

        AttendanceSession::create([
            'course_id' => $request->course_id,
            'session_date' => $request->session_date,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'session_type' => $request->session_type,
            'location_id' => $request->session_type == 'onsite' ? $request->location_id : null,
            'description' => $request->description,
        ]);

        return redirect()->route('lecturer.dashboard')
            ->with('success', 'Sesi presensi berhasil dibuat!');
    }
}
