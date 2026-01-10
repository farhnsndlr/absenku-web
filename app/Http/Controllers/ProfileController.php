<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;

class ProfileController extends Controller
{
    private function getLecturerDashboardData($user)
    {
        $totalCourses = 0;
        $totalStudents = 0;
        $averageAttendance = 0;
        $todaysSessions = collect();
        $courseAttendanceStatus = [];

        if ($user->role === 'lecturer' && $user->profile instanceof LecturerProfile) {
            $coursesQuery = $user->profile->courses();
            $coursesTaught = $coursesQuery->get();
            $totalCourses = $coursesQuery->count();
            $courseIds = $coursesQuery->pluck('id')->toArray();

            if (!empty($courseIds)) {
                $totalStudents = DB::table('course_enrollments')
                    ->whereIn('course_id', $courseIds)
                    ->distinct('student_profile_id')
                    ->count();

                $sessionIds = DB::table('attendance_sessions')
                    ->whereIn('course_id', $courseIds)
                    ->pluck('id')
                    ->toArray();

                if (!empty($sessionIds)) {
                    $totalRecords = DB::table('attendance_records')
                        ->whereIn('session_id', $sessionIds)
                        ->count();
                    $presentRecords = DB::table('attendance_records')
                        ->whereIn('session_id', $sessionIds)
                        ->where('status', 'present')
                        ->count();

                    if ($totalRecords > 0) {
                        $averageAttendance = round(($presentRecords / $totalRecords) * 100, 2);
                    }
                }

                $rawSessions = DB::table('attendance_sessions')
                    ->select('attendance_sessions.*', 'courses.course_code', 'courses.course_name')
                    ->join('courses', 'attendance_sessions.course_id', '=', 'courses.id')
                    ->whereIn('attendance_sessions.course_id', $courseIds)
                    ->whereDate('attendance_sessions.start_time', Carbon::today())
                    ->orderBy('attendance_sessions.start_time', 'asc')
                    ->get();

                $now = Carbon::now();
                $todaysSessions = $rawSessions->map(function ($session) use ($now) {
                    $startTime = Carbon::parse($session->start_time);
                    $endTime = Carbon::parse($session->end_time);

                    if ($now->lessThan($startTime)) {
                        $session->time_status = 'Upcoming';
                        $session->status_badge = 'bg-yellow-100 text-yellow-800';
                    } elseif ($now->greaterThanOrEqualTo($startTime) && $now->lessThanOrEqualTo($endTime)) {
                        $session->time_status = 'Active';
                        $session->status_badge = 'bg-blue-100 text-blue-800';
                    } else {
                        $session->time_status = 'Finished';
                        $session->status_badge = 'bg-gray-100 text-gray-800';
                    }
                    return $session;
                });

                foreach ($coursesTaught as $course) {
                    $courseSessionIds = DB::table('attendance_sessions')
                        ->where('course_id', $course->id)
                        ->pluck('id')
                        ->toArray();

                    $lastSession = DB::table('attendance_sessions')
                        ->where('course_id', $course->id)
                        ->orderBy('start_time', 'desc')
                        ->select('start_time as session_date')
                        ->first();

                    $sessionCount = DB::table('attendance_sessions')
                        ->where('course_id', $course->id)
                        ->count();

                    $percentage = 0;

                    if (!empty($courseSessionIds)) {
                        $courseTotalRecords = DB::table('attendance_records')
                            ->whereIn('session_id', $courseSessionIds)
                            ->count();

                        $coursePresentRecords = DB::table('attendance_records')
                            ->whereIn('session_id', $courseSessionIds)
                            ->where('status', 'present')
                            ->count();

                        if ($courseTotalRecords > 0) {
                            $percentage = ($coursePresentRecords / $courseTotalRecords) * 100;
                        }
                    }

                    $courseAttendanceStatus[] = [
                        'course' => $course,
                        'last_session' => $lastSession,
                        'session_count' => $sessionCount,
                        'percentage' => round($percentage, 1),
                    ];
                }
            }
        }

        return compact('totalCourses', 'totalStudents', 'averageAttendance', 'todaysSessions', 'courseAttendanceStatus');
    }

    // Menampilkan profil beserta data sesuai role.
    public function show()
    {
        $user = Auth::user();
        $user->load('profile');

        $dashboardData = $this->getLecturerDashboardData($user);

        $stats = [];
        $todaysSchedule = collect();
        $recentHistory = collect();

        if ($user->role === 'student' && $user->studentProfile) {
            $studentProfileId = $user->studentProfile->id;
            $todayStr = Carbon::today()->toDateString();

            $rawStats = AttendanceRecord::where('student_id', $studentProfileId)
                ->selectRaw('
                    sum(status = "present") as present,
                    sum(status = "late") as late,
                    sum(status IN ("permit", "sick")) as permit,
                    sum(status = "absent") as absent
                ')
                ->first();

            $stats = [
                'present' => $rawStats->present ?? 0,
                'late'    => $rawStats->late ?? 0,
                'permit'  => $rawStats->permit ?? 0,
                'absent'  => $rawStats->absent ?? 0,
                'total_attendance' => ($rawStats->present ?? 0) + ($rawStats->late ?? 0),
            ];

            $enrolledCourseIds = $user->studentProfile->courses()->pluck('courses.id');

            $todaysSchedule = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
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
                ->take(5)
                ->get();
        }

        $additionalData = [];
        if ($user->role === 'student' && $user->profile instanceof StudentProfile) {
            $coursesEnrolled = $user->profile->courses()->with('lecturer.profile')->get();
            $additionalData['courses_enrolled'] = $coursesEnrolled;

            $studentClasses = $coursesEnrolled
                ->pluck('pivot.class_name')
                ->filter()
                ->unique()
                ->values();

            $additionalData['student_classes'] = $studentClasses;
        }

        if ($user->role === 'lecturer' && $user->profile instanceof LecturerProfile) {
            $additionalData['courses_taught'] = $user->profile->courses()->get();
        }

        if (isset($recentHistory) && $recentHistory instanceof Collection) {
            $recentHistoryPage = max(1, (int) request()->query('recent_history_page', 1));
            $recentHistory = new LengthAwarePaginator(
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
        }

        return view('profile.show', array_merge([
            'user' => $user,
            'additionalData' => $additionalData,
            'dashboardView' => $this->getDashboardView(),
            'stats' => $stats,
            'todaysSchedule' => $todaysSchedule,
            'recentHistory' => $recentHistory,
        ], $dashboardData));
    }

    private function getDashboardView()
    {
        $roleViews = [
            'admin' => 'admin.dashboard',
            'lecturer' => 'lecturer.dashboard',
            'student' => 'student.dashboard',
        ];

        $role = Auth::user()->role;
        return $roleViews[$role] ?? 'layouts.dashboard';
    }

    // Menampilkan form edit profil.
    public function edit()
    {
        $user = Auth::user();
        $dashboardData = $this->getLecturerDashboardData($user);
        $dashboardView = $this->getDashboardView();
        if ($user->role === 'student') {
            $dashboardView = 'layouts.dashboard';
        }
        return view('profile.edit', array_merge([
            'user' => $user,
            'dashboardView' => $dashboardView,
        ], $dashboardData));
    }

    // Memperbarui data profil.
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $validatedData['profile_photo_path'] = $path;
        }

        if ($validatedData['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->update(\Illuminate\Support\Arr::only($validatedData, ['name', 'email', 'profile_photo_path']));

        $profileData = array_filter($request->only(['phone_number', 'nid', 'npm', 'class_name', 'full_name']), function ($value) {
            return !is_null($value) && $value !== '';
        });

        if (!empty($profileData)) {
            $user->profile()->update($profileData);
        }

        return redirect()->route('profile.show')->with('status', 'profile-updated');
    }

    // Menampilkan form ubah password.
    public function editPassword()
    {
        $user = Auth::user();
        $dashboardData = $this->getLecturerDashboardData($user);
        $dashboardView = $this->getDashboardView();
        if ($user->role === 'student') {
            $dashboardView = 'layouts.dashboard';
        }
        return view('profile.password', array_merge([
            'dashboardView' => $dashboardView,
        ], $dashboardData));
    }

    // Memperbarui password akun.
    public function updatePassword(PasswordUpdateRequest $request)
    {
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);
        return redirect()->route('profile.show')->with('status', 'password-updated');
    }
}
