<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ReportController extends Controller
{
    // Menampilkan laporan dengan filter.
    public function index(Request $request)
    {
        $user = Auth::user();
        $isLecturer = $user->role === 'lecturer';

        $query = AttendanceSession::with(['course', 'location']);

        $this->applyFilters($query, $request);

        if ($isLecturer) {
            $query->whereHas('course', function (Builder $q) use ($user) {
                $q->where('lecturer_id', $user->id);
            });
        } else {
            if ($request->filled('lecturer_id')) {
                $query->whereHas('course', function (Builder $q) use ($request) {
                    $q->where('lecturer_id', $request->lecturer_id);
                });
            }
        }

        $sessions = $query->latest('session_date')
            ->latest('start_time')
            ->paginate(15)
            ->withQueryString();

        $statistics = $this->calculateStatistics($request, $isLecturer, $user->id);

        $coursesQuery = Course::orderBy('course_name');
        $classNamesQuery = AttendanceSession::distinct()->orderBy('class_name');

        if ($isLecturer) {
            $coursesQuery->where('lecturer_id', $user->id);
            $classNamesQuery->whereHas('course', function ($q) use ($user) {
                $q->where('lecturer_id', $user->id);
            });
            $lecturers = collect();
        } else {
            $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        }

        $courses = $coursesQuery->get();
        $classNames = $classNamesQuery->pluck('class_name')->filter();

        $viewName = $isLecturer ? 'lecturer.reports.index' : 'admin.reports.index';

        return view($viewName, compact(
            'sessions',
            'statistics',
            'courses',
            'classNames',
            'lecturers'
        ));
    }

    // Menampilkan detail laporan per sesi.
    public function show($sessionId)
    {
        $user = Auth::user();
        $isLecturer = $user->role === 'lecturer';

        $session = AttendanceSession::with([
            'course',
            'location',
            'attendanceRecords.user'
        ])->findOrFail($sessionId);

        if ($isLecturer && $session->course->lecturer_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan sesi ini.');
        }

        $records = $session->attendanceRecords;

        $totalPresent = $records->whereIn('status', ['present', 'late'])->count();
        $totalRecords = $records->count();

        $sessionStats = [
            'total_students' => $totalRecords,
            'present' => $totalPresent,
            'absent' => $records->where('status', 'absent')->count(),
            'sick' => $records->whereIn('status', ['sick', 'permit'])->count(),
            'attendance_rate' => $totalRecords > 0
                ? round(($totalPresent / $totalRecords) * 100, 1)
                : 0
        ];

        $viewName = $isLecturer ? 'lecturer.reports.show' : 'admin.reports.show';
        return view($viewName, compact('session', 'records', 'sessionStats'));
    }

    // Mengekspor laporan presensi.
    public function export(Request $request)
    {
        $filters = $request->all();
        $user = Auth::user();

        if ($user->role === 'lecturer') {
            $filters['lecturer_id'] = $user->id;
        }

        $filename = 'Laporan_Absensi_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new AttendanceReportExport($filters), $filename);
    }

    private function applyFilters(Builder $query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->where('session_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('session_date', '<=', $request->end_date);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('learning_type') || $request->filled('session_type')) {
            $learningType = $request->input('learning_type', $request->input('session_type'));
            $query->where('learning_type', $learningType);
        }
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }
    }

    private function calculateStatistics(Request $request, $isLecturer, $lecturerId)
    {
        $query = AttendanceSession::query();

        $this->applyFilters($query, $request);

        if ($isLecturer) {
            $query->whereHas('course', function ($q) use ($lecturerId) {
                $q->where('lecturer_id', $lecturerId);
            });
        } elseif ($request->filled('lecturer_id')) {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('lecturer_id', $request->lecturer_id);
            });
        }

        $totalSessions = (clone $query)->count();
        $onlineSessions = (clone $query)->where('learning_type', 'online')->count();
        $offlineSessions = (clone $query)->where('learning_type', 'offline')->count();

        $sessionIds = $query->pluck('id');

        $recordStats = AttendanceRecord::whereIn('session_id', $sessionIds)
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(status in ("present","late")) as present')
            ->selectRaw('sum(status = "absent") as absent')
            ->selectRaw('sum(status in ("sick","permit")) as sick')
            ->first();

        return [
            'total_sessions' => $totalSessions,
            'total_records' => $recordStats->total ?? 0,
            'present' => $recordStats->present ?? 0,
            'absent' => $recordStats->absent ?? 0,
            'sick' => $recordStats->sick ?? 0,
            'online_sessions' => $onlineSessions,
            'offline_sessions' => $offlineSessions,
        ];
    }
}
