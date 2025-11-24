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
// Impor DB facade untuk query manual
use Illuminate\Support\Facades\DB;
use App\Models\Course;
// Import Collection helper
use Illuminate\Support\Collection;
use Carbon\Carbon; // Import Carbon untuk manipulasi waktu

class ProfileController extends Controller
{

    /**
     * Menghitung dan mengumpulkan data statistik dashboard untuk Dosen.
     * Logika ini digunakan oleh show(), edit(), dan editPassword().
     * * @param \App\Models\User $user
     * @return array
     */
    private function getLecturerDashboardData($user)
    {
        // Inisialisasi semua variabel dashboard
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
                // Hitung Total Siswa (Mahasiswa) Unik
                $totalStudents = DB::table('course_enrollments')
                                    ->whereIn('course_id', $courseIds)
                                    ->distinct('student_profile_id')
                                    ->count();

                // Hitung Rata-rata Presensi Keseluruhan
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

                // Ambil Sesi Presensi Hari Ini
                $rawSessions = DB::table('attendance_sessions')
                                    ->select('attendance_sessions.*', 'courses.course_code', 'courses.course_name')
                                    ->join('courses', 'attendance_sessions.course_id', '=', 'courses.id')
                                    // Memastikan kolom yang ambigu diberikan prefix
                                    ->whereIn('attendance_sessions.course_id', $courseIds)
                                    ->whereDate('attendance_sessions.start_time', Carbon::today())
                                    ->orderBy('attendance_sessions.start_time', 'asc')
                                    ->get();

                // Hitung properti time_status
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

                // Hitung Status Absensi untuk Setiap Mata Kuliah
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


    /**
     * Menampilkan halaman profil pengguna (Method show).
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('profile');

        $dashboardData = $this->getLecturerDashboardData($user);

        $additionalData = [];
        if ($user->role === 'student' && $user->profile instanceof StudentProfile) {
            $additionalData['courses_enrolled'] = $user->profile->courses()->get();
        }

        return view('profile.show', array_merge([
            'user' => $user,
            'additionalData' => $additionalData,
            'dashboardView' => $this->getDashboardView(),
        ], $dashboardData));
    }


    private function getDashboardView()
    {
        // Mapping antara role dan file view dashboard-nya
        $roleViews = [
            'admin' => 'admin.dashboard',
            'lecturer' => 'lecturer.dashboard',
            'student' => 'student.dashboard',
        ];

        $role = Auth::user()->role;

        // Kembalikan nama view yang sesuai role,
        // Jika role tidak dikenali, fallback ke layout utama 'layouts.dashboard'
        return $roleViews[$role] ?? 'layouts.dashboard';
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        $dashboardData = $this->getLecturerDashboardData($user);

        // Kita kirim semua variabel yang dibutuhkan ke view profile.edit
        return view('profile.edit', array_merge([
            'user' => $user,
            'dashboardView' => $this->getDashboardView()
        ], $dashboardData));
    }

    /**
     * Memproses update profil.
     */
    public function update(ProfileUpdateRequest $request)
    {
        // Data sudah divalidasi oleh ProfileUpdateRequest
        $user = $request->user();
        // Ambil semua data yang sudah divalidasi
        $validatedData = $request->validated();

        // --- 1. Handle Upload Foto ---
        if ($request->hasFile('photo')) {
            // a. Hapus foto lama jika ada (agar storage tidak penuh)
            if ($user->profile_photo_path) {
                // Gunakan disk 'public' karena kita menyimpannya di sana
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // b. Simpan foto baru
            // store('profile-photos', 'public') akan menyimpan file di storage/app/public/profile-photos
            // dan mengembalikan path-nya (misal: profile-photos/namafileunik.jpg)
            $path = $request->file('photo')->store('profile-photos', 'public');

            // c. Masukkan path ke array data yang akan diupdate ke tabel users
            $validatedData['profile_photo_path'] = $path;
        }

        // --- 2. Update data di tabel users ---

        // Cek apakah email diubah. Jika ya, reset status verifikasi email.
        // PENTING: Pengecekan ini harus dilakukan SEBELUM data disimpan.
        // Kita bandingkan email baru dari request dengan email lama di user.
        if ($validatedData['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        // Lakukan update massal pada model User.
        // Kita hanya mengambil field yang relevan untuk tabel users.
        $user->update(\Illuminate\Support\Arr::only($validatedData, ['name', 'email', 'profile_photo_path']));


        // --- 3. Update data di tabel profil polimorfik (lecturer_profiles / student_profiles) ---

        // Ambil data yang relevan untuk profil tambahan
        // KUNCI PERBAIKAN: Memasukkan 'full_name' yang merupakan kolom untuk nama formal di LecturerProfile
        $profileData = array_filter($request->only(['phone_number', 'nid', 'npm', 'full_name']), function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Jika ada data profil yang dikirim, lakukan update melalui relasi polimorfik
        if (!empty($profileData)) {
            $user->profile()->update($profileData);
        }

        // Redirect kembali ke halaman show dengan pesan sukses
        return redirect()->route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Menampilkan form ubah password.
     */
    public function editPassword()
    {
        $user = Auth::user();
        $dashboardData = $this->getLecturerDashboardData($user);

        // Kita kirim semua variabel yang dibutuhkan ke view profile.password
        return view('profile.password', array_merge([
            'dashboardView' => $this->getDashboardView(),
        ], $dashboardData));
    }

    /**
     * Memproses update password.
     */
    public function updatePassword(PasswordUpdateRequest $request)
    {
        // Data sudah divalidasi. Password lama sudah dicek cocok.
        $user = $request->user();

        // Update password dengan yang baru dan di-hash
        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()->route('profile.show')->with('status', 'password-updated');
    }
}
