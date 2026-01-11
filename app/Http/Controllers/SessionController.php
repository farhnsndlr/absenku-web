<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SessionController extends Controller
{
    // Menampilkan daftar sesi per mata kuliah.
    public function index($courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->lecturer_id !== Auth::user()->profile_id) {
            abort(403, 'Anda tidak memiliki akses ke mata kuliah ini.');
        }

        $sessions = $course->sessions()->latest()->get();

        return view('lecturer.sessions.index', compact('course', 'sessions'));
    }

    // Menampilkan form pembuatan sesi.
    public function create()
    {
        $courses = Course::where('lecturer_id', Auth::user()->profile_id)->get();

        if ($courses->isEmpty()) {
            return redirect()->route('lecturer.dashboard')->with('error', 'Anda belum memiliki mata kuliah.');
        }

        $locations = Location::all();
        return view('lecturer.sessions.create', compact('courses', 'locations'));
    }

    // Menyimpan sesi baru.
    public function store(Request $request)
    {
        $lecturerProfileId = Auth::user()->profile_id;

        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) use ($lecturerProfileId) {
                    return $query->where('lecturer_id', $lecturerProfileId);
                }),
            ],
            'session_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'learning_type' => 'required|in:online,offline',
            'location_id' => 'required_if:learning_type,offline|nullable|exists:locations,id',
            'lateness_limit' => 'required|integer|min:0',
            'topic' => 'nullable|string|max:255',
        ], [
            'course_id.exists' => 'Mata kuliah tidak valid atau Anda tidak memiliki akses.',
        ]);

        $token = strtoupper(Str::random(8));
        $tokenExpiresAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->session_date . ' ' . $request->end_time
        );

        AttendanceSession::create([
            'course_id' => $request->course_id,
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'learning_type' => $request->learning_type,
            'location_id' => $request->learning_type === 'offline' ? $request->location_id : null,
            'lateness_limit' => $request->lateness_limit,
            'session_token' => $token,
            'session_token_expires_at' => $tokenExpiresAt,
            'description' => $request->topic,
        ]);

        return redirect()->route('lecturer.sessions.index', $request->course_id)
            ->with('success', "Sesi berhasil dibuat! Token: $token");
    }


    // Memproses presensi mahasiswa via token.
    public function studentCheckIn(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $inputToken = strtoupper($request->token);
        $student = Auth::user();

        $session = AttendanceSession::where('session_token', $inputToken)->with('course')->first();

        if (!$session) {
            return back()->with('error', 'Token sesi tidak valid atau sesi tidak ditemukan.');
        }


        $now = Carbon::now();
        $sessionStart = Carbon::parse($session->session_date . ' ' . $session->start_time);
        $sessionEnd = Carbon::parse($session->session_date . ' ' . $session->end_time);

        if ($now->lessThan($sessionStart)) {
            return back()->with('error', 'Sesi presensi belum dimulai.');
        }

        if ($now->greaterThan($sessionEnd)) {
            return back()->with('error', 'Sesi presensi sudah berakhir.');
        }

        $alreadyCheckedIn = $session->attendances()
            ->where('student_id', Auth::user()->profile_id)
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah melakukan presensi untuk sesi ini.');
        }

        $diffMinutes = $sessionStart->diffInMinutes($now, false);

        $status = 'on_time';
        if ($diffMinutes > $session->lateness_limit) {
            $status = 'late';
        }

        $session->attendances()->create([
            'student_id' => Auth::user()->profile_id,
            'check_in_time' => $now,
            'status' => $status,
        ]);

        $message = $status === 'on_time' ? 'Presensi berhasil! Tepat waktu.' : 'Presensi berhasil, namun Anda tercatat terlambat.';

        return back()->with('success', $message);
    }
}
