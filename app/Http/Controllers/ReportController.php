<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\User; // Ganti StudentProfile dengan User
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isLecturer = $user->role === 'lecturer';

        // 1. Query Dasar untuk Sesi
        $query = AttendanceSession::with(['course', 'location']);

        // 2. Terapkan Filter Global (Tanggal, Mata Kuliah, Tipe Sesi, Nama Kelas)
        $this->applyFilters($query, $request);

        // 3. Filter Spesifik Role
        if ($isLecturer) {
            // Dosen hanya melihat sesi dari MK yang diajarnya
            $query->whereHas('course', function (Builder $q) use ($user) {
                $q->where('lecturer_id', $user->id);
            });
        } else {
            // Admin bisa filter berdasarkan dosen tertentu
            if ($request->filled('lecturer_id')) {
                $query->whereHas('course', function (Builder $q) use ($request) {
                    $q->where('lecturer_id', $request->lecturer_id);
                });
            }
        }

        // 4. Ambil Data Sesi (Paginated)
        $sessions = $query->latest('session_date')
            ->latest('start_time')
            ->paginate(15)
            ->withQueryString(); // Agar filter tetap ada saat pindah halaman

        // 5. Hitung Statistik (Berdasarkan filter yang sama)
        $statistics = $this->calculateStatistics($request, $isLecturer, $user->id);

        // 6. Ambil Data untuk Dropdown Filter
        $coursesQuery = Course::orderBy('course_name');
        $classNamesQuery = AttendanceSession::distinct()->orderBy('class_name');

        if ($isLecturer) {
            // Dropdown dosen hanya berisi data miliknya
            $coursesQuery->where('lecturer_id', $user->id);
            $classNamesQuery->whereHas('course', function ($q) use ($user) {
                $q->where('lecturer_id', $user->id);
            });
            $lecturers = collect(); // Dosen tidak butuh filter dosen
        } else {
            // Dropdown admin berisi semua data
            $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        }

        $courses = $coursesQuery->get();
        $classNames = $classNamesQuery->pluck('class_name')->filter();

        // 7. Tentukan View Berdasarkan Role
        $viewName = $isLecturer ? 'lecturer.reports.index' : 'admin.reports.index';

        return view($viewName, compact(
            'sessions',
            'statistics',
            'courses',
            'classNames',
            'lecturers'
        ));
    }

    public function show($sessionId)
    {
        $user = Auth::user();
        $isLecturer = $user->role === 'lecturer';

        // Eager load yang benar: course, location, dan record beserta mahasiswanya
        $session = AttendanceSession::with([
            'course',
            'location',
            'attendanceRecords.user'
        ])->findOrFail($sessionId);

        // Cek Otorisasi Dosen
        if ($isLecturer && $session->course->lecturer_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan sesi ini.');
        }

        $records = $session->attendanceRecords; // Sudah di-eager load

        // Statistik untuk sesi ini
        $totalPresent = $records->where('status', 'present')->count();
        $totalRecords = $records->count();

        $sessionStats = [
            'total_students' => $totalRecords,
            'present' => $totalPresent,
            'absent' => $records->where('status', 'absent')->count(),
            'sick' => $records->where('status', 'sick')->count(),
            'attendance_rate' => $totalRecords > 0
                ? round(($totalPresent / $totalRecords) * 100, 1)
                : 0
        ];

        $viewName = $isLecturer ? 'lecturer.reports.show' : 'admin.reports.show';
        return view($viewName, compact('session', 'records', 'sessionStats'));
    }

    public function export(Request $request)
    {
        $filters = $request->all();
        $user = Auth::user();

        // Jika lecturer, tambahkan paksa filter lecturer_id
        if ($user->role === 'lecturer') {
            $filters['lecturer_id'] = $user->id;
        }

        $filename = 'Laporan_Absensi_' . now()->format('Ymd_His') . '.xlsx';

        // Menggunakan class export baru yang sudah kita optimalkan
        return Excel::download(new AttendanceReportExport($filters), $filename);
    }

    /**
     * Menerapkan filter umum ke query builder.
     */
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
        // Gunakan nama kolom yang konsisten: learning_type
        if ($request->filled('learning_type')) {
            $query->where('learning_type', $request->learning_type);
        }
        // Filter baru: Nama Kelas
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }
    }

    /**
     * Menghitung statistik global berdasarkan filter.
     */
    private function calculateStatistics(Request $request, $isLecturer, $lecturerId)
    {
        $query = AttendanceSession::query();

        // Terapkan filter global yang sama
        $this->applyFilters($query, $request);

        // Terapkan filter role
        if ($isLecturer) {
            $query->whereHas('course', function ($q) use ($lecturerId) {
                $q->where('lecturer_id', $lecturerId);
            });
        } elseif ($request->filled('lecturer_id')) {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('lecturer_id', $request->lecturer_id);
            });
        }

        // Hitung statistik sesi
        // Gunakan clone() agar query dasar tidak berubah
        $totalSessions = (clone $query)->count();
        $onlineSessions = (clone $query)->where('learning_type', 'online')->count();
        $offlineSessions = (clone $query)->where('learning_type', 'offline')->count();

        // Hitung statistik record (menggunakan whereIn session_id hasil filter)
        // Ini lebih efisien daripada memuat semua record
        $sessionIds = $query->pluck('id');

        // Gunakan query builder untuk agregat record
        $recordStats = AttendanceRecord::whereIn('session_id', $sessionIds)
            ->selectRaw('count(*) as total, sum(status = "present") as present, sum(status = "absent") as absent, sum(status = "sick") as sick')
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
