<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;

class CourseController extends Controller
{
    // Menampilkan daftar mata kuliah.
    public function index()
    {
        $courses = Course::with('lecturer')->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    // Menampilkan form tambah mata kuliah.
    public function create()
    {
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        return view('admin.courses.create', compact('lecturers'));
    }

    // Menyimpan mata kuliah baru.
    public function store(CourseStoreRequest $request)
    {
        $validated = $request->validated();
        Course::create($validated);
        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata kuliah baru berhasil ditambahkan.');
    }

    // Menampilkan form edit mata kuliah.
    public function edit(Course $course)
    {
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'lecturers'));
    }

    // Memperbarui data mata kuliah.
    public function update(CourseUpdateRequest $request, Course $course)
    {
        $validated = $request->validated();
        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Data mata kuliah berhasil diperbarui.');
    }

    // Menghapus mata kuliah.
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    // Menampilkan daftar mata kuliah dosen.
    public function lecturerIndex()
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->with(['enrollments', 'sessions'])
            ->orderBy('created_at', 'desc')
            ->get();

        $statistics = [
            'total_courses' => $courses->count(),
            'total_students' => $courses->sum(function ($course) {
                return $course->enrollments->count();
            }),
            'total_sessions' => $courses->sum(function ($course) {
                return $course->sessions->count();
            }),
        ];

        return view('lecturer.courses.index', compact('courses', 'statistics'));
    }

    // Menampilkan detail mata kuliah untuk dosen.
    public function lecturerShow(Course $course)
    {
        $lecturer = Auth::user();

        if ($course->lecturer_id !== $lecturer->id) {
            abort(403, 'Unauthorized');
        }

        $course->load(['enrollments.studentProfile', 'sessions']);

        $stats = [
            'total_students' => $course->enrollments()->count(),
            'total_sessions' => $course->sessions()->count(),
            'total_attendance_records' => $course->sessions()
                ->withCount('attendanceRecords')
                ->get()
                ->sum('attendance_records_count'),
        ];

        return view('lecturer.courses.show', compact('course', 'stats'));
    }

    // Menampilkan form tambah mata kuliah (dosen).
    public function lecturerCreate()
    {
        $lecturer = Auth::user();

        $availableCourses = Course::where('lecturer_id', null)
            ->orWhere('lecturer_id', '!=', $lecturer->id)
            ->orderBy('course_code')
            ->get();

        $locations = Location::orderBy('location_name')->get();

        return view('lecturer.courses.create', compact('availableCourses', 'locations')); 
    }

    // Menyimpan mata kuliah (dosen).
    public function lecturerStore(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ], [
            'course_id.required' => 'Pilih mata kuliah terlebih dahulu',
            'course_id.exists' => 'Mata kuliah tidak ditemukan',
        ]);

        $lecturer = Auth::user();
        $course = Course::findOrFail($validated['course_id']);

        if ($course->lecturer_id !== null && $course->lecturer_id !== $lecturer->id) {
            return back()->with('error', 'Mata kuliah ini sudah di-assign ke dosen lain');
        }

        $course->update(['lecturer_id' => $lecturer->id]);

        return redirect()->route('lecturer.classes.index')
            ->with('success', 'Kelas ' . $course->course_name . ' berhasil ditambahkan');
    }

    // Menampilkan form edit mata kuliah (dosen).
    public function lecturerEdit(Course $course)
    {
        if ($course->lecturer_id !== Auth::user()->id) {
            abort(403, 'Unauthorized');
        }

        return view('lecturer.courses.edit', compact('course'));
    }

    // Memperbarui mata kuliah (dosen).
    public function lecturerUpdate(Request $request, Course $course)
    {
        if ($course->lecturer_id !== Auth::user()->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'course_code' => 'required|string|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:255',
            'sks' => 'nullable|integer|min:1|max:6',
            'description' => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $course->update($validated);

        return redirect()->route('lecturer.classes.show', $course)
            ->with('success', 'Kelas berhasil diperbarui');
    }

    // Menghapus mata kuliah (dosen).
    public function lecturerDestroy(Course $course)
    {
        if ($course->lecturer_id !== Auth::user()->id) {
            abort(403, 'Unauthorized');
        }

        if ($course->sessions()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus kelas yang sudah memiliki sesi');
        }

        $course->delete();

        return redirect()->route('lecturer.classes.index')
            ->with('success', 'Kelas berhasil dihapus');
    }
}
