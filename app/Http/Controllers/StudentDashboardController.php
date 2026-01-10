<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\StudentProfile;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentDashboardController extends Controller
{
    // Menyiapkan data untuk dashboard mahasiswa.
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
             abort(403, 'Akses ditolak. Halaman ini hanya untuk mahasiswa.');
        }

        $studentProfile = $user->studentProfile;

        if (!$studentProfile) {
            abort(403, 'Profil mahasiswa tidak ditemukan. Silakan hubungi admin.');
        }

        $studentProfileId = $studentProfile->id;

        $todayStr = Carbon::today()->toDateString();
        $now = Carbon::now();
        $joinedAt = $user->created_at ?? $now;
        $hasEnrollments = $studentProfile->courses()->exists();
        $enrolledCourseIds = $hasEnrollments ? $studentProfile->courses()->pluck('courses.id') : collect();
        $studentClassNames = $hasEnrollments
            ? $studentProfile->courses()
                ->pluck('course_enrollments.class_name')
                ->filter()
                ->unique()
                ->values()
                ->all()
            : array_values(array_filter([trim((string) ($studentProfile->class_name ?? ''))]));

        $recordsForStats = AttendanceRecord::with('session')
            ->where('student_id', $studentProfileId)
            ->get();

        $presentCount = 0;
        $lateCount = 0;
        $permitCount = 0;
        $absentCount = 0;

        foreach ($recordsForStats as $record) {
            $status = $record->status;

            if (in_array($status, ['permit', 'sick'], true)) {
                $permitCount++;
                continue;
            }

            if ($status === 'absent') {
                $absentCount++;
                continue;
            }

            $session = $record->session;
            if (!$session || !$record->submission_time) {
                if ($status === 'present') {
                    $presentCount++;
                } elseif ($status === 'late') {
                    $lateCount++;
                }
                continue;
            }

            $tolerance = max(0, (int) ($session->late_tolerance_minutes ?? 10));
            $lateThreshold = $session->end_date_time->copy()->addMinutes($tolerance);

            if ($record->submission_time->lte($session->end_date_time)) {
                $presentCount++;
            } elseif ($record->submission_time->lte($lateThreshold)) {
                $lateCount++;
            } else {
                $lateCount++;
            }
        }

        $absentMissing = 0;
        if ($hasEnrollments) {
            $absentMissing = AttendanceSession::leftJoin('attendance_records', function ($join) use ($studentProfileId) {
                $join->on('attendance_sessions.id', '=', 'attendance_records.session_id')
                    ->where('attendance_records.student_id', $studentProfileId);
            })
                ->whereIn('attendance_sessions.course_id', $enrolledCourseIds)
                ->when(!empty($studentClassNames), function ($query) use ($studentClassNames) {
                    $query->whereIn('attendance_sessions.class_name', $studentClassNames);
                }, function ($query) {
                    $query->whereNull('attendance_sessions.id');
                })
                ->whereDate('attendance_sessions.session_date', '>=', $joinedAt->toDateString())
                ->whereRaw(
                    'date_add(timestamp(attendance_sessions.session_date, time(attendance_sessions.end_time)), interval ifnull(attendance_sessions.late_tolerance_minutes, 10) minute) < ?',
                    [$now->toDateTimeString()]
                )
                ->whereNull('attendance_records.id')
                ->count();
        }

        $stats = [
            'present' => $presentCount,
            'late' => $lateCount,
            'permit' => $permitCount,
            'absent' => $absentCount + $absentMissing,
            'total_attendance' => $presentCount + $lateCount,
        ];


        $todaysSchedule = AttendanceSession::when($hasEnrollments, function ($query) use ($enrolledCourseIds) {
                $query->whereIn('course_id', $enrolledCourseIds);
            })
            ->when(!empty($studentClassNames), function ($query) use ($studentClassNames) {
                $query->whereIn('class_name', $studentClassNames);
            }, function ($query) {
                $query->whereNull('id');
            })
            ->whereDate('session_date', $todayStr)
            ->with([
                'course.lecturer.profile',
                'location',
                'records' => function ($q) use ($studentProfileId) {
                    $q->where('student_id', $studentProfileId);
                }
            ])
            ->orderBy('start_time', 'asc')
            ->get();


        $recentHistory = AttendanceRecord::where('student_id', $studentProfileId)
            ->with(['session.course'])
            ->orderBy('submission_time', 'desc')
            ->take(25)
            ->get();

        $recentAbsentSessions = collect();
        if ($hasEnrollments) {
            $recentAbsentSessions = AttendanceSession::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->when(!empty($studentClassNames), function ($query) use ($studentClassNames) {
                    $query->whereIn('class_name', $studentClassNames);
                }, function ($query) {
                    $query->whereNull('id');
                })
                ->whereDate('session_date', '<=', $todayStr)
                ->whereDate('session_date', '>=', $joinedAt->toDateString())
                ->whereDoesntHave('records', function ($query) use ($studentProfileId) {
                    $query->where('student_id', $studentProfileId);
                })
                ->get()
                ->filter(function ($session) use ($now) {
                    $tolerance = max(0, (int) ($session->late_tolerance_minutes ?? 10));
                    $lateDeadline = $session->end_date_time->copy()->addMinutes($tolerance);
                    return $lateDeadline->lt($now);
                })
                ->map(function ($session) use ($now, $studentProfileId) {
                    $tolerance = max(0, (int) ($session->late_tolerance_minutes ?? 10));
                    $lateDeadline = $session->end_date_time->copy()->addMinutes($tolerance);

                    $record = new AttendanceRecord([
                        'session_id' => $session->id,
                        'student_id' => $studentProfileId,
                        'status' => 'absent',
                        'submission_time' => $lateDeadline,
                    ]);
                    $record->setRelation('session', $session);
                    $record->computed_status = 'absent';
                    return $record;
                });
        }

        $recentHistory = $recentHistory
            ->merge($recentAbsentSessions)
            ->sortByDesc(function ($record) {
                return $record->submission_time;
            })
            ->values();

        $recentHistory->each(function ($record) {
            $status = $record->status;

            if (in_array($status, ['permit', 'sick', 'absent'], true)) {
                $record->computed_status = $status;
                return;
            }

            if (!$record->session || !$record->submission_time) {
                $record->computed_status = $status;
                return;
            }

            $tolerance = max(0, (int) ($record->session->late_tolerance_minutes ?? 10));
            $lateThreshold = $record->session->end_date_time->copy()->addMinutes($tolerance);

            if ($record->submission_time->lte($record->session->end_date_time)) {
                $record->computed_status = 'present';
            } elseif ($record->submission_time->lte($lateThreshold)) {
                $record->computed_status = 'late';
            } else {
                $record->computed_status = 'late';
            }
        });


        $recentHistoryPage = max(1, (int) request()->query('recent_history_page', 1));
        $recentHistoryPaginator = new LengthAwarePaginator(
            $recentHistory->forPage($recentHistoryPage, 5)->values(),
            $recentHistory->count(),
            5,
            $recentHistoryPage,
            [
                'path' => request()->url(),
                'pageName' => 'recent_history_page',
                'query' => request()->query(),
            ]
        );

        return view('student.dashboard', [
            'stats' => $stats,
            'todaysSchedule' => $todaysSchedule,
            'recentHistory' => $recentHistoryPaginator,
        ]);
    }
}
