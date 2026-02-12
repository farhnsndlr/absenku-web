<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\StudentProfile;

class StudentAttendanceController extends Controller
{
    // Menampilkan daftar sesi presensi hari ini untuk mahasiswa.
    public function index()
    {
        $user = Auth::user();
        $student = $this->getStudentProfile($user);

        $now = Carbon::now();
        $today = $now->toDateString();

        $courseEnrollments = $this->loadEnrollments($student);
        $hasEnrollments = $courseEnrollments->isNotEmpty();
        $enrolledCourseIds = $courseEnrollments->pluck('id')->all();
        $studentClassNames = $hasEnrollments
            ? $courseEnrollments
                ->pluck('pivot.class_name')
                ->filter()
                ->unique()
                ->values()
                ->all()
            : array_values(array_filter([trim((string) ($student->class_name ?? ''))]));

        $sessions = AttendanceSession::with([
            'course',
            'location',
            'records' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }
        ])
            ->when($hasEnrollments, function ($query) use ($enrolledCourseIds) {
                $query->whereIn('course_id', $enrolledCourseIds);
            })
            ->when(!empty($studentClassNames), function ($query) use ($studentClassNames) {
                $query->whereIn('class_name', $studentClassNames);
            }, function ($query) {
                $query->whereNull('id');
            })
            ->whereDate('session_date', $today)
            ->select([
                'id',
                'course_id',
                'session_date',
                'start_time',
                'end_time',
                'learning_type',
                'location_id',
                'session_token',
                'class_name',
                'late_tolerance_minutes',
            ])
            ->orderBy('start_time', 'asc')
            ->paginate(5);

        $sessions->getCollection()->transform(function ($session) use ($now) {

            $start = $session->start_date_time;
            $end = $session->end_date_time;
            $tolerance = max(0, (int) ($session->late_tolerance_minutes ?? 10));
            $endWithTolerance = $end->copy()->addMinutes($tolerance);

            if ($now->lt($start)) {
                $session->time_status = 'upcoming';
            } elseif ($now->between($start, $endWithTolerance)) {
                $session->time_status = 'ongoing';
            } else {
                $session->time_status = 'finished';
            }

            $session->is_ongoing = $session->time_status === 'ongoing';
            $session->is_finished = $session->time_status === 'finished';
            $session->has_checked_in = $session->records?->isNotEmpty() ?? false;

            return $session;
        });

        return view('student.attendance.index', [
            'user' => $user,
            'activeSessions' => $sessions->getCollection(),
            'activeSessionsPaginator' => $sessions,
        ]);
    }


    // Menyimpan presensi mahasiswa pada sesi tertentu.
    public function store(Request $request, $sessionId)
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;

        if (!$studentProfile) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $session = AttendanceSession::with(['course', 'location'])->find($sessionId);

        if (!$session) {
            return back()->with('error', 'Sesi presensi tidak ditemukan.');
        }

        $rules = [
            'status' => 'required|in:present,permit,sick',
            'proof_photo' => 'required_without:photo_base64|image|mimes:jpeg,png,jpg|max:2048',
            'photo_base64' => 'required_without:proof_photo|string',
            'supporting_document' => 'required_if:status,permit,sick|file|mimes:jpeg,png,jpg,pdf|max:4096',
        ];

        $messages = [
            'status.required' => 'Harap pilih status kehadiran.',
            'proof_photo.required_without' => 'Foto bukti presensi wajib diambil.',
            'photo_base64.required_without' => 'Foto bukti presensi wajib diambil.',
            'proof_photo.image' => 'File foto harus berupa gambar.',
            'supporting_document.required_if' => 'Bukti izin/sakit wajib diupload.',
        ];

        if ($session->session_token) {
            $rules['token'] = 'required|string|size:6';
            $messages['token.required'] = 'Token presensi wajib diisi.';
            $messages['token.size'] = 'Token presensi harus 6 karakter.';
        }

        if ($session->learning_type === 'offline' && $request->input('status') === 'present') {
            $rules['latitude'] = 'required|numeric';
            $rules['longitude'] = 'required|numeric';
            $messages['latitude.required'] = 'Gagal mendapatkan lokasi. Pastikan GPS aktif.';
        }

        $request->validate($rules, $messages);

        if ($session->session_token) {
            $inputToken = strtoupper($request->input('token', ''));
            if ($inputToken !== $session->session_token) {
                return back()->with('error', 'Token presensi tidak sesuai.');
            }
        }

        if ($session->status === 'closed') {
            return back()->with('error', 'Sesi presensi ini sudah ditutup.');
        }

        $enrollments = $this->loadEnrollments($studentProfile);
        $enrolledCourseIds = $enrollments->pluck('id');
        if ($enrolledCourseIds->isNotEmpty() && !$enrolledCourseIds->contains($session->course_id)) {
            return back()->with('error', 'Anda tidak terdaftar pada mata kuliah ini.');
        }

        $classError = $this->getClassAccessError($session, $studentProfile, $enrollments);
        if ($classError) {
            return back()->with('error', $classError);
        }

        $alreadyCheckedIn = AttendanceRecord::where('session_id', $session->id)
            ->where('student_id', $studentProfile->id)
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah melakukan presensi pada sesi ini.');
        }

        $now = Carbon::now();
        [$startTime, $endTime] = $this->resolveSessionTimes($session);

        $tolerance = max(0, (int) ($session->late_tolerance_minutes ?? 10));
        $lateDeadline = $endTime->copy()->addMinutes($tolerance);

        if ($now->lt($startTime)) {
            return back()->with('error', 'Sesi presensi belum dimulai.');
        }

        if ($now->gt($lateDeadline)) {
            if ($request->input('status') === 'present') {
                AttendanceRecord::create([
                    'session_id' => $session->id,
                    'student_id' => $studentProfile->id,
                    'status' => 'absent',
                    'submission_time' => $now,
                    'learning_type' => $session->learning_type,
                ]);

                return redirect()->route('student.dashboard')
                    ->with('error', 'Waktu presensi sudah lewat. Anda tercatat alpa.');
            }

            return back()->with('error', 'Waktu presensi untuk sesi ini sudah berakhir.');
        }



        if ($session->learning_type === 'offline' && $request->input('status') === 'present') {
            if (!$session->location) {
                return back()->with('error', 'Data lokasi sesi belum diatur oleh dosen. Hubungi dosen.');
            }

            $distance = $this->calculateDistance(
                (float)$request->latitude,
                (float)$request->longitude,
                (float)$session->location->latitude,
                (float)$session->location->longitude
            );

            $allowedRadius = $session->location->radius_meters ?? 100;

            if ($distance > $allowedRadius) {
                return back()->with('error', 'Anda tidak berada di area kampus.');
            }
        }
        try {
            $photoPath = null;
            if ($request->hasFile('proof_photo')) {
                $photoPath = $request->file('proof_photo')->store('attendance_proofs', 'public');
            } else {
                $photoPath = $this->saveBase64Image($request->input('photo_base64'));
            }

            if (!$photoPath) {
                return back()->with('error', 'Gagal menyimpan foto presensi. Silakan coba lagi.');
            }
            $documentPath = null;
            if ($request->hasFile('supporting_document')) {
                $documentPath = $request->file('supporting_document')->store('attendance_supporting_documents', 'public');
            }


            $statusInput = $request->status;
            if ($statusInput === 'present') {
                $status = $now->lte($endTime) ? 'present' : 'late';
            } else {
                $status = $statusInput;
            }
            $locationMaps = null;
            if ($statusInput === 'present' && $request->filled('latitude') && $request->filled('longitude')) {
                $locationMaps = $request->latitude . ',' . $request->longitude;
            }
            AttendanceRecord::create([
                'session_id' => $session->id,
                'student_id' => $studentProfile->id,
                'status' => $status,
                'submission_time' => $now,
                'photo_path' => $photoPath,
                'supporting_document_path' => $documentPath,
                'location_maps' => $locationMaps,
                'learning_type' => $session->learning_type,
            ]);

            $message = $status === 'present'
                ? 'Presensi berhasil! Tepat waktu.'
                : ($status === 'late' ? 'Presensi berhasil, namun Anda tercatat terlambat.' : 'Presensi berhasil tercatat.');
            return redirect()->route('student.dashboard')->with('success', $message);
        } catch (\Exception $e) {
            if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            if (isset($documentPath) && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }
    // Menampilkan form presensi untuk sesi tertentu.
    public function checkinForm($sessionId)
    {
        $user = Auth::user();
        $studentProfile = $this->getStudentProfile($user);

        $session = AttendanceSession::with(['course', 'location'])->findOrFail($sessionId);

        if ($session->status === 'closed') {
            return redirect()->route('student.attendance.index')
                ->with('error', 'Sesi presensi ini sudah ditutup.');
        }

        $now = Carbon::now();
        [$startTime, $endTime] = $this->resolveSessionTimes($session);

        $tolerance = max(0, (int) ($session->late_tolerance_minutes ?? 10));
        $lateDeadline = $endTime->copy()->addMinutes($tolerance);

        if (!$now->between($startTime, $lateDeadline)) {
            return redirect()->route('student.attendance.index')
                ->with('error', 'Waktu presensi untuk sesi ini sudah di luar jadwal.');
        }

        $enrollments = $this->loadEnrollments($studentProfile);
        $enrolledCourseIds = $enrollments->pluck('id');
        if ($enrolledCourseIds->isNotEmpty() && !$enrolledCourseIds->contains($session->course_id)) {
            return redirect()->route('student.attendance.index')
                ->with('error', 'Anda tidak terdaftar pada mata kuliah ini.');
        }

        $classError = $this->getClassAccessError($session, $studentProfile, $enrollments);
        if ($classError) {
            return redirect()->route('student.attendance.index')
                ->with('error', $classError);
        }

        $alreadyCheckedIn = AttendanceRecord::where('session_id', $session->id)
            ->where('student_id', $studentProfile->id)
            ->exists();

        if ($alreadyCheckedIn) {
            return redirect()->route('student.attendance.index')
                ->with('warning', 'Anda sudah melakukan presensi pada sesi ini.');
        }

        if ($session->learning_type === 'offline' && !$session->location) {
            return redirect()->route('student.attendance.index')
                ->with('error', 'Data lokasi sesi belum diatur oleh dosen. Hubungi dosen.');
        }

        return view('student.attendance.form', [
            'session' => $session,
            'location' => $session->location,
        ]);
    }

    private function getStudentProfile($user)
    {
        if ($user->role !== 'student') abort(403);
        $profile = $user->studentProfile;
        if (!$profile) abort(403, 'Profil mahasiswa tidak ditemukan.');
        return $profile;
    }

    private function getClassAccessError(AttendanceSession $session, StudentProfile $studentProfile, $enrollments = null): ?string
    {
        if (!$session->class_name) {
            return null;
        }

        $enrollments = $enrollments ?? $this->loadEnrollments($studentProfile);
        $hasEnrollments = $enrollments->isNotEmpty();

        if ($hasEnrollments) {
            $courseEnrollments = $enrollments->where('id', $session->course_id);
            if ($courseEnrollments->isEmpty()) {
                return 'Anda tidak terdaftar pada mata kuliah ini.';
            }

            $classNames = $courseEnrollments
                ->pluck('pivot.class_name')
                ->filter();

            if (!$classNames->contains($session->class_name)) {
                return 'Anda tidak terdaftar pada kelas sesi ini.';
            }

            return null;
        }

        $studentClass = trim((string) ($studentProfile->class_name ?? ''));
        if ($studentClass === '') {
            return 'Kelas mahasiswa belum diatur. Silakan lengkapi profil atau hubungi admin.';
        }

        if ($session->class_name !== $studentClass) {
            return 'Anda tidak terdaftar pada kelas sesi ini.';
        }

        return null;
    }


    private function saveBase64Image(?string $base64)
    {
        if (!$base64) return null;

        if (preg_match('/^data:(image\/\w+);base64,/', $base64, $type)) {
            $data = base64_decode(substr($base64, strpos($base64, ',') + 1));
            if ($data === false) return null;

            $ext = explode('/', $type[1])[1];
            $filename = 'attendance_' . time() . '_' . uniqid() . '.' . $ext;
            $path = 'attendance_photos/' . $filename;

            Storage::disk('public')->put($path, $data);
            return $path;
        }

        return null;
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function loadEnrollments(StudentProfile $studentProfile)
    {
        return $studentProfile->courses()
            ->select('courses.id')
            ->withPivot('class_name')
            ->get();
    }

    private function resolveSessionTimes(AttendanceSession $session): array
    {
        $sessionDate = $session->session_date instanceof Carbon
            ? $session->session_date->toDateString()
            : substr((string) $session->session_date, 0, 10);

        $startTimeValue = $session->start_time instanceof Carbon
            ? $session->start_time->format('H:i:s')
            : (string) $session->start_time;
        $endTimeValue = $session->end_time instanceof Carbon
            ? $session->end_time->format('H:i:s')
            : (string) $session->end_time;

        return [
            Carbon::parse($sessionDate . ' ' . $startTimeValue),
            Carbon::parse($sessionDate . ' ' . $endTimeValue),
        ];
    }

    // Menampilkan form input token presensi.
    public function showTokenForm()
    {
        return view('student.attendance.token-form');
    }

    // Memvalidasi token dan mencatat presensi.
    public function processToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:6',
        ], [
            'token.required' => 'Harap masukkan token presensi.',
            'token.size' => 'Token harus terdiri dari 6 karakter.',
        ]);

        $user = Auth::user();
        if (!$user->studentProfile) {
            return back()->with('error', 'Data profil mahasiswa tidak ditemukan.');
        }
        $studentProfile = $user->studentProfile;

        $inputToken = strtoupper($request->token);

        $session = AttendanceSession::where('session_token', $inputToken)
            ->with('course')
            ->first();


        if (!$session) {
            return back()->withErrors(['token' => 'Token tidak valid atau sesi tidak ditemukan.'])->withInput();
        }

        if ($session->status !== 'open') {
            return back()->with('error', 'Sesi presensi ini sudah ditutup atau belum dibuka oleh dosen.');
        }

        $now = Carbon::now();
        $tolerance = max(0, (int) ($session->late_tolerance_minutes ?? 10));
        $lateDeadline = $session->end_datetime->copy()->addMinutes($tolerance);

        if (!$now->between($session->start_datetime, $lateDeadline)) {
            return back()->with('error', 'Waktu presensi untuk sesi ini sudah berakhir.');
        }

        $enrollments = $this->loadEnrollments($studentProfile);
        $enrolledCourseIds = $enrollments->pluck('id');
        if ($enrolledCourseIds->isNotEmpty() && !$enrolledCourseIds->contains($session->course_id)) {
            return back()->with('error', 'Anda tidak terdaftar di mata kuliah ini (' . $session->course->course_name . '). Presensi ditolak.');
        }

        $classError = $this->getClassAccessError($session, $studentProfile, $enrollments);
        if ($classError) {
            return back()->with('error', $classError);
        }

        $alreadyCheckedIn = AttendanceRecord::where('session_id', $session->id)
            ->where('student_id', $studentProfile->id)
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah berhasil melakukan presensi pada sesi ini sebelumnya.');
        }


        try {
            AttendanceRecord::create([
                'session_id' => $session->id,
                'student_id' => $studentProfile->id,
                'status' => 'present',
                'submission_time' => $now,
            ]);

            return redirect()->route('student.dashboard')->with('success', 'Presensi berhasil! Kehadiran Anda di mata kuliah ' . $session->course->course_name . ' telah tercatat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data. Silakan coba lagi.');
        }
    }
}
