<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendanceSession::with(['course', 'location']);

        if (Auth::user()->role === 'lecturer') {
            $query->where('lecturer_id', Auth::user()->id);
        }
        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->where('session_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('session_date', '<=', $request->end_date);
        }

        // Filter berdasarkan mata kuliah
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter berdasarkan tipe pembelajaran
        if ($request->filled('learning_type')) {
            $query->where('learning_type', $request->learning_type);
        }

        $sessions = $query->latest('session_date')->paginate(15);

        // Hitung statistik
        $statistics = $this->calculateStatistics($request);

        // Ambil data untuk filter dropdown
        $courses = Course::orderBy('course_name')->get();

        if (Auth::user()->role === 'lecturer') {
            return view('lecturer.reports.index', compact('sessions', 'statistics', 'courses'));
        }
        return view('admin.reports.index', compact('sessions', 'statistics', 'courses'));
    }

    public function show($sessionId)
    {
        $session = AttendanceSession::with(['course', 'location'])->findOrFail($sessionId);

        $records = AttendanceRecord::with(['student', 'student.user'])
            ->where('session_id', $sessionId)
            ->get();

        if (Auth::user()->role === 'lecturer' && $session->lecturer_id !== Auth::user()->id) {
            abort(403, 'Tidak punya akses ke sesi ini.');
        }

        // Statistik untuk session ini
        $sessionStats = [
            'total_students' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'sick' => $records->where('status', 'sick')->count(),
            'attendance_rate' => $records->count() > 0
                ? round(($records->where('status', 'present')->count() / $records->count()) * 100, 2)
                : 0
        ];

        if (Auth::user()->role === 'lecturer') {
            return view('lecturer.reports.show', compact('session', 'records', 'sessionStats'));
        }
        return view('admin.reports.show', compact('session', 'records', 'sessionStats'));
    }

    public function export(Request $request)
    {
        $filters = $request->all();

        // Jika lecturer, batasi hanya kelas dia
        if (Auth::user()->role === 'lecturer') {
            $filters['lecturer_id'] = Auth::user()->id;
        }

        $filename = 'laporan-kehadiran-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new AttendanceReportExport($filters), $filename);
    }


    private function calculateStatistics($request)
    {
        $query = AttendanceSession::query();

        // Apply same filters
        if ($request->filled('start_date')) {
            $query->where('session_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('session_date', '<=', $request->end_date);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('session_type')) {
            $query->where('session_type', $request->session_type);
        }

        $sessionIds = $query->pluck('id');

        $recordsQuery = AttendanceRecord::whereIn('session_id', $sessionIds);

        return [
            'total_sessions' => $query->count(),
            'total_records' => $recordsQuery->count(),
            'present' => $recordsQuery->where('status', 'present')->count(),
            'absent' => $recordsQuery->where('status', 'absent')->count(),
            'sick' => $recordsQuery->where('status', 'sick')->count(),
            'online_sessions' => $query->where('learning_type', 'online')->count(),
            'offline_sessions' => $query->where('learning_type', 'offline')->count(),
        ];
    }
}
