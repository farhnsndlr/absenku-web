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
use App\Notifications\NewSessionCreated;

class LecturerSessionController extends Controller
{
    // Menampilkan daftar sesi milik dosen.
    public function index()
    {
        $lecturerId = Auth::id();

        $sessions = AttendanceSession::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
            ->with(['course', 'location'])
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        $totalCourses = Course::where('lecturer_id', $lecturerId)->count();

        $totalSessions = AttendanceSession::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })->count();

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

    // Menampilkan form pembuatan sesi.
    public function create()
    {
        $lecturerId = Auth::id();

        $myCourses = Course::where('lecturer_id', $lecturerId)
            ->with('location')
            ->orderBy('course_name')
            ->get();

        $locations = Location::orderBy('location_name')->get();

        if ($myCourses->isEmpty()) {
            return redirect()->route('lecturer.dashboard')
                ->with('error', 'Anda belum memiliki mata kuliah yang diampu. Hubungi Admin.');
        }

        $coursesDefaults = $myCourses->mapWithKeys(function ($course) {
            return [$course->id => [
                'start_time' => $course->start_time ? Carbon::parse($course->start_time)->format('H:i') : null,
                'end_time' => $course->end_time ? Carbon::parse($course->end_time)->format('H:i') : null,
                'learning_type' => $course->session_type,
                'location_id' => $course->location_id,
            ]];
        });

        $totalCourses = $myCourses->count();

        return view('lecturer.sessions.create', compact('myCourses', 'locations', 'coursesDefaults', 'totalCourses'));
    }

    // Menyimpan sesi baru dan mengirim notifikasi.
    public function store(StoreSessionRequest $request)
    {
        $validated = $request->validated();

        $validated['session_token'] = Str::upper(Str::random(6));
        $validated['lecturer_id'] = Auth::id();

        $validated['status'] = 'scheduled';

        if ($validated['learning_type'] === 'online') {
            $validated['location_id'] = null;
        }

        $session = AttendanceSession::create($validated);

        $className = trim((string) ($session->class_name ?? ''));
        $students = collect();

        if ($className !== '') {
            $students = $session->course->students()
                ->wherePivot('class_name', $className)
                ->with('user')
                ->get()
                ->map(function ($student) {
                    return $student->user;
                })
                ->filter()
                ->unique('id')
                ->values();
        }

        foreach ($students as $user) {
            $user->notify(new NewSessionCreated($session));
        }

        return redirect()->route('lecturer.sessions.index')
            ->with('success', 'Sesi kelas baru berhasil dijadwalkan.');
    }

    // Menampilkan detail dan rekap sesi.
    public function show(AttendanceSession $session)
    {
        if ($session->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $session->load(['course', 'location', 'attendanceRecords.student']);

        $totalStudents = $session->attendanceRecords->count();
        $presentCount = $session->attendanceRecords->where('status', 'present')->count();
        $absentCount = $session->attendanceRecords->where('status', 'absent')->count();
        $sickCount = $session->attendanceRecords->where('status', 'sick')->count();

        $attendanceRate = $totalStudents > 0
            ? round(($presentCount / $totalStudents) * 100, 2)
            : 0;

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

    // Menampilkan form edit sesi.
    public function edit($id)
    {
        $session = AttendanceSession::with(['course', 'location'])
            ->findOrFail($id);
        $locations = Location::all();
        $courses = Course::where('lecturer_id', Auth::user()->id)->get();

        return view('lecturer.sessions.edit', compact('session', 'locations', 'courses'));
    }

    // Memperbarui data sesi.
    public function update(Request $request, $id)
    {
        $session = AttendanceSession::with('course')->findOrFail($id);

        if ($session->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'class_name' => ['required', 'string', 'max:50'],
            'learning_type' => ['required', 'in:online,offline'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:180'],
            'location_id' => [
                'required_if:learning_type,offline',
                'nullable',
                Rule::exists('locations', 'id'),
            ],
            'topic' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,open,closed'],
        ]);

        try {
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
            $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
        } catch (\Exception $e) {
            return back()->withErrors(['end_time' => 'Format jam tidak valid.'])->withInput();
        }

        if ($endTime->lte($startTime)) {
            return back()->withErrors(['end_time' => 'Waktu selesai harus lebih akhir dari waktu mulai.'])->withInput();
        }

        if ($validated['learning_type'] === 'online') {
            $validated['location_id'] = null;
        }

        $session->update($validated);

        return redirect()->route('lecturer.sessions.show', $session->id)
            ->with('success', 'Sesi presensi berhasil diperbarui.');
    }

    // Menghapus sesi.
    public function destroy(AttendanceSession $session)
    {
        if ($session->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $session->delete();

        return redirect()->route('lecturer.sessions.index')
            ->with('success', 'Sesi kelas berhasil dihapus.');
    }
}
