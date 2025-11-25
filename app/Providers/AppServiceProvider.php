<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // View Composer untuk semua view lecturer
        View::composer('lecturer.*', function ($view) {
            if (Auth::check() && Auth::user()->role === 'lecturer') {
                $lecturerId = Auth::id();

                // Hitung data yang dibutuhkan untuk dashboard
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

                // Hitung total students dari semua attendance records
                $totalStudents = AttendanceRecord::whereHas('session', function ($query) use ($lecturerId) {
                    $query->whereHas('course', function ($q) use ($lecturerId) {
                        $q->where('lecturer_id', $lecturerId);
                    });
                })->distinct('student_id')->count('student_id');

                $totalPresent = AttendanceRecord::whereHas('session', function ($query) use ($lecturerId) {
                    $query->whereHas('course', function ($q) use ($lecturerId) {
                        $q->where('lecturer_id', $lecturerId);
                    });
                })
                    ->where('status', 'present')
                    ->count();

                $totalAbsent = AttendanceRecord::whereHas('session', function ($query) use ($lecturerId) {
                    $query->whereHas('course', function ($q) use ($lecturerId) {
                        $q->where('lecturer_id', $lecturerId);
                    });
                })
                    ->where('status', 'absent')
                    ->count();

                // Hitung rata-rata kehadiran
                $totalRecords = AttendanceRecord::whereHas('session', function ($query) use ($lecturerId) {
                    $query->whereHas('course', function ($q) use ($lecturerId) {
                        $q->where('lecturer_id', $lecturerId);
                    });
                })->count();

                $averageAttendance = $totalRecords > 0
                    ? round(($totalPresent / $totalRecords) * 100, 2)
                    : 0;

                // Ambil sesi hari ini dengan data tambahan
                $todaysSessions = AttendanceSession::whereHas('course', function ($query) use ($lecturerId) {
                    $query->where('lecturer_id', $lecturerId);
                })
                    ->with(['course', 'location'])
                    ->where('session_date', now()->toDateString())
                    ->orderBy('start_time')
                    ->get()
                    ->map(function ($session) {
                        // Tambahkan properti dinamis untuk kemudahan akses di view
                        $now = Carbon::now();
                        $startTime = $session->start_time->copy()->setDate(
                            Carbon::parse($session->session_date)->year,
                            Carbon::parse($session->session_date)->month,
                            Carbon::parse($session->session_date)->day
                        );

                        $endTime = $session->end_time->copy()->setDate(
                            Carbon::parse($session->session_date)->year,
                            Carbon::parse($session->session_date)->month,
                            Carbon::parse($session->session_date)->day
                        );


                        if ($now->between($startTime, $endTime)) {
                            $session->time_status = 'Active';
                        } elseif ($now->lt($startTime)) {
                            $session->time_status = 'Upcoming';
                        } else {
                            $session->time_status = 'Finished';
                        }

                        $session->course_name = $session->course->course_name ?? 'N/A';
                        $session->course_code = $session->course->course_code ?? 'N/A';
                        $session->location_name = $session->location->location_name ?? 'Online';

                        return $session;
                    });

                // Status kehadiran per mata kuliah
                $courses = Course::where('lecturer_id', $lecturerId)->get();
                $courseAttendanceStatus = $courses->map(function ($course) {
                    $sessions = AttendanceSession::where('course_id', $course->id)->get();
                    $sessionCount = $sessions->count();

                    $totalRecordsForCourse = AttendanceRecord::whereIn('session_id', $sessions->pluck('id'))->count();
                    $presentForCourse = AttendanceRecord::whereIn('session_id', $sessions->pluck('id'))
                        ->where('status', 'present')
                        ->count();

                    $percentage = $totalRecordsForCourse > 0
                        ? round(($presentForCourse / $totalRecordsForCourse) * 100, 2)
                        : 0;

                    $lastSession = $sessions->sortByDesc('session_date')->first();

                    return [
                        'course' => $course,
                        'session_count' => $sessionCount,
                        'percentage' => $percentage,
                        'last_session' => $lastSession,
                    ];
                });

                // Share ke semua view
                $view->with([
                    'totalCourses' => $totalCourses,
                    'totalSessions' => $totalSessions,
                    'upcomingSessions' => $upcomingSessions,
                    'completedSessions' => $completedSessions,
                    'totalStudents' => $totalStudents,
                    'totalPresent' => $totalPresent,
                    'totalAbsent' => $totalAbsent,
                    'averageAttendance' => $averageAttendance,
                    'todaysSessions' => $todaysSessions,
                    'courseAttendanceStatus' => $courseAttendanceStatus,
                ]);
            }
        });
    }
}
