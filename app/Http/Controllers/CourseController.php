<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('lecturer')->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        // Ambil user dengan role 'lecturer'
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        return view('admin.courses.create', compact('lecturers'));
    }

    public function store(CourseStoreRequest $request)
    {
        // Data sudah divalidasi otomatis oleh Form Request
        $validated = $request->validated();

        // Simpan data ke database
        Course::create($validated);

        // Redirect dengan flash message 'success' agar toast muncul
        return redirect()->route('admin.courses.index')
                         ->with('success', 'Mata kuliah baru berhasil ditambahkan.');
    }

    public function edit(Course $course)
    {
        // Sama seperti create, kita butuh daftar dosen untuk dropdown edit
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'lecturers'));
    }

   public function update(CourseUpdateRequest $request, Course $course)
    {
        // Data sudah divalidasi
        $validated = $request->validated();

        // Update data di database
        $course->update($validated);

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Data mata kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        // Gunakan flash message 'success' agar toast notification muncul
        return redirect()->route('admin.courses.index')
                         ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
