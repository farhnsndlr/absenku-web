<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LecturerDashboardController extends Controller
{
    // Menyiapkan data untuk dashboard dosen.
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'lecturer' || !$user->lecturerProfile) {
            abort(403, 'Akses ditolak. Akun ini bukan dosen atau profil dosen belum ada.');
        }
        $lecturerId = $user->lecturerProfile->id;

        $tanggalHariIni = Carbon::today();

        $taughtCourses = Course::where('lecturer_id', $lecturerId)->get();
        $totalCourses = $taughtCourses->count();

        $totalStudents = DB::table('course_enrollments')
            ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
            ->where('courses.lecturer_id', $lecturerId)
            ->distinct('course_enrollments.student_profile_id')
            ->count('course_enrollments.student_profile_id');

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $attendanceSummary = AttendanceRecord::join('attendance_sessions', 'attendance_records.session_id', '=', 'attendance_sessions.id')
            ->join('courses', 'attendance_sessions.course_id', '=', 'courses.id')
            ->where('courses.lecturer_id', $lecturerId)
            ->whereBetween('attendance_sessions.session_date', [$startOfWeek, $endOfWeek])
            ->selectRaw('count(*) as total_records')
            ->selectRaw('sum(status = "present") as total_present')
            ->first();

        $totalPresent = (int) ($attendanceSummary->total_present ?? 0);
        $totalRecords = (int) ($attendanceSummary->total_records ?? 0);

        $averageAttendance = $totalRecords > 0 ? ($totalPresent / $totalRecords) * 100 : 0;


        $todaysSessions = AttendanceSession::with(['course', 'location'])
            ->whereHas('course', function ($query) use ($lecturerId) {
                $query->where('lecturer_id', $lecturerId);
            })
            ->whereDate('session_date', $tanggalHariIni)
            ->orderBy('start_time')
            ->get();

        foreach ($todaysSessions as $session) {
            $now = Carbon::now();
            try {
                $startTimeOnly = Carbon::parse($session->start_time)->format('H:i:s');
                $endTimeOnly = Carbon::parse($session->end_time)->format('H:i:s');

                $startTime = Carbon::parse($session->session_date->format('Y-m-d') . ' ' . $startTimeOnly);
                $endTime = Carbon::parse($session->session_date->format('Y-m-d') . ' ' . $endTimeOnly);
            } catch (\Exception $e) {
                $session->time_status = 'error_parsing_time';
                continue;
            }

            if ($now > $endTime) {
                $session->time_status = 'finished';
            } elseif ($now >= $startTime && $now <= $endTime) {
                $session->time_status = 'ongoing';
            } else {
                $session->time_status = 'upcoming';
            }
        }



        $courseIds = $taughtCourses->pluck('id');

        $courseRecordStats = AttendanceRecord::join('attendance_sessions', 'attendance_records.session_id', '=', 'attendance_sessions.id')
            ->whereIn('attendance_sessions.course_id', $courseIds)
            ->select('attendance_sessions.course_id')
            ->selectRaw('count(*) as total_records')
            ->selectRaw('sum(status = "present") as present')
            ->groupBy('attendance_sessions.course_id')
            ->get()
            ->keyBy('course_id');

        $sessionCounts = AttendanceSession::whereIn('course_id', $courseIds)
            ->where('session_date', '<=', Carbon::now())
            ->select('course_id')
            ->selectRaw('count(*) as session_count')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $lastSessions = AttendanceSession::whereIn('course_id', $courseIds)
            ->where('session_date', '<=', Carbon::now())
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get()
            ->groupBy('course_id')
            ->map->first();

        $courseAttendanceStatus = [];

        foreach ($taughtCourses as $course) {
            $recordStats = $courseRecordStats->get($course->id);
            $presentCount = (int) ($recordStats->present ?? 0);
            $totalRecordsCourse = (int) ($recordStats->total_records ?? 0);
            $percentageCourse = $totalRecordsCourse > 0 ? ($presentCount / $totalRecordsCourse) * 100 : 0;

            $sessionCount = (int) ($sessionCounts->get($course->id)->session_count ?? 0);
            $lastSession = $lastSessions->get($course->id);

            $courseAttendanceStatus[] = [
                'course' => $course,
                'last_session' => $lastSession,
                'session_count' => $sessionCount,
                'percentage' => $percentageCourse,
            ];
        }

        return view('lecturer.dashboard', compact(
            'totalCourses',
            'totalStudents',
            'averageAttendance',
            'todaysSessions',
            'courseAttendanceStatus'
        ));
    }
}
