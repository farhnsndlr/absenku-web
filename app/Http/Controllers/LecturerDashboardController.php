<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\User;
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


        $totalCourses = Course::where('lecturer_id', $lecturerId)
            ->count();

        $totalStudents = Course::where('lecturer_id', $lecturerId)
            ->with('students')
            ->get()
            ->pluck('students')
            ->flatten()
            ->unique('id')
            ->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $attendancesThisWeek = AttendanceRecord::whereHas('session.course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
            ->whereHas('session', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('session_date', [$startOfWeek, $endOfWeek]);
            })
            ->get();

        $totalPresent = $attendancesThisWeek->where('status', 'present')->count();
        $totalRecords = $attendancesThisWeek->count();

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



        $taughtCourses = Course::where('lecturer_id', $lecturerId)
            ->get();

        $courseAttendanceStatus = [];

        foreach ($taughtCourses as $course) {
            $totalPresentCourse = AttendanceRecord::whereHas('session', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->where('status', 'present')->count();

            $totalRecordsCourse = AttendanceRecord::whereHas('session', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->count();

            $percentageCourse = $totalRecordsCourse > 0 ? ($totalPresentCourse / $totalRecordsCourse) * 100 : 0;

            $lastSession = AttendanceSession::where('course_id', $course->id)
                ->where('session_date', '<=', Carbon::now())
                ->orderBy('session_date', 'desc')
                ->orderBy('start_time', 'desc')
                ->first();

            $sessionCount = AttendanceSession::where('course_id', $course->id)
                ->where('session_date', '<=', Carbon::now())
                ->count();

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
