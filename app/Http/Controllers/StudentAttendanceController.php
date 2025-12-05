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
    public function index()
    {
        $user = Auth::user();
        $student = $this->getStudentProfile($user);

        $now = Carbon::now();
        $today = $now->toDateString();

        // Ambil course yg diambil mahasiswa
        $enrolledCourseIds = $student->courses()->pluck('courses.id')->toArray();

        // Ambil sesi khusus mata kuliah mahasiswa saja
        $sessions = AttendanceSession::with(['course', 'location'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->whereDate('session_date', $today)
            ->select([
                'id',
                'course_id',
                'session_date',
                'start_time',
                'end_time',
                'learning_type',
                'location_id',
            ])
            ->get();

        // Hitung waktu dan status
        $activeSessions = $sessions->map(function ($session) use ($now) {

            $start = $session->start_date_time;
            $end = $session->end_date_time;

            if ($now->lt($start)) {
                $session->time_status = 'upcoming';
            } elseif ($now->between($start, $end)) {
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
            'activeSessions' => $activeSessions,
        ]);
    }


    public function store(Request $request, $sessionId)
    {

        // 1. Validasi Input Awal (Token & Foto Wajib)
        $request->validate([
            'token' => 'required|string|size:6',
            // Validasi foto: harus gambar, max 2MB
            'proof_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'token.required' => 'Token sesi wajib diisi.',
            'proof_photo.required' => 'Bukti foto wajib diupload.',
            'proof_photo.image' => 'File harus berupa gambar.',
        ]);

        $user = Auth::user();
        $studentProfile = $user->studentProfile; // Asumsi relasi sudah benar

        if (!$studentProfile) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // 2. Cari Sesi Berdasarkan TOKEN
        $inputToken = strtoupper($request->token);
        // Eager load course dan location untuk efisiensi
        $session = AttendanceSession::where('session_token', $inputToken)
            ->with(['course', 'location'])
            ->first();

        // --- VALIDASI UMUM (Berlaku untuk Online & Offline) ---

        if (!$session) {
            return back()->withErrors(['token' => 'Token tidak valid atau sesi tidak ditemukan.'])->withInput();
        }

        if ($session->status !== 'open') {
            return back()->with('error', 'Sesi presensi ini sudah ditutup.');
        }

        // Cek Waktu
        $now = Carbon::now();
        // Asumsi model AttendanceSession punya accessor 'start_datetime' dan 'end_datetime' yang mengembalikan objek Carbon
        if (!$now->between($session->start_datetime, $session->end_datetime)) {
            return back()->with('error', 'Waktu presensi untuk sesi ini sudah di luar jadwal.');
        }

        // Cek Enrollment (Apakah mahasiswa mengambil matkul ini?)
        $isEnrolled = $studentProfile->courses()
            ->where('courses.id', $session->course_id)
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'Anda tidak terdaftar pada mata kuliah ini.');
        }

        // Cek Double Check-in
        $alreadyCheckedIn = AttendanceRecord::where('session_id', $session->id)
            ->where('student_id', $studentProfile->id)
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah melakukan presensi pada sesi ini.');
        }


        // --- VALIDASI KONDISIONAL (ONLINE vs OFFLINE) ---

        // Jika sesi OFFLINE, wajib validasi lokasi
        if ($session->learning_type === 'offline') {
            // Validasi input koordinat harus ada
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ], [
                'latitude.required' => 'Gagal mendapatkan lokasi. Pastikan GPS aktif dan izin lokasi diberikan untuk sesi Offline.',
            ]);

            if (!$session->location) {
                return back()->with('error', 'Data lokasi sesi belum diatur oleh dosen. Hubungi dosen.');
            }

            // Hitung Jarak
            $distance = $this->calculateDistance(
                (float)$request->latitude,
                (float)$request->longitude,
                (float)$session->location->latitude,
                (float)$session->location->longitude
            );

            // Ambil radius toleransi (default 100m jika tidak diset)
            $allowedRadius = $session->location->radius_meters ?? 100;

            if ($distance > $allowedRadius) {
                return back()->with('error', "Anda berada di luar radius lokasi presensi. Jarak Anda: " . round($distance) . "m (Max: {$allowedRadius}m).");
            }
        }
        // JIKA SESSION ONLINE: Kode di atas dilewati, tidak ada pengecekan lokasi.


        // --- PROSES PENYIMPANAN DATA ---

        try {
            // 1. Upload Foto
            $photoPath = null;
            if ($request->hasFile('proof_photo')) {
                // Simpan di storage/app/public/attendance_proofs
                $photoPath = $request->file('proof_photo')->store('attendance_proofs', 'public');
            }

            // 2. Tentukan Status (Hadir/Telat)
            // Asumsi ada kolom late_tolerance_minutes di tabel session, default 15 menit
            $tolerance = $session->late_tolerance_minutes ?? 15;
            $lateThreshold = $session->start_datetime->copy()->addMinutes($tolerance);
            $status = $now->lte($lateThreshold) ? 'present' : 'late';

            // 3. Simpan Record Presensi
            AttendanceRecord::create([
                'session_id' => $session->id,
                'student_id' => $studentProfile->id,
                'status' => $status,
                'submission_time' => $now,
                'proof_photo' => $photoPath, // Path foto yang baru diupload
                // Simpan koordinat jika ada (baik online/offline, sebagai data tambahan)
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $message = $status === 'present' ? 'Presensi berhasil! Tepat waktu.' : 'Presensi berhasil, namun Anda tercatat terlambat.';
            return redirect()->route('student.dashboard')->with('success', $message);
        } catch (\Exception $e) {
            // Hapus foto jika sudah terlanjur ke-upload tapi database gagal
            if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }


    private function getStudentProfile($user)
    {
        if ($user->role !== 'student') abort(403);
        $profile = $user->studentProfile;
        if (!$profile) abort(403, 'Profil mahasiswa tidak ditemukan.');
        return $profile;
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

    public function showTokenForm()
    {
        return view('student.attendance.token-form');
    }

    public function processToken(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'token' => 'required|string|size:6', // Token harus 6 karakter
        ], [
            'token.required' => 'Harap masukkan token presensi.',
            'token.size' => 'Token harus terdiri dari 6 karakter.',
        ]);

        // Ambil user mahasiswa yang sedang login
        $user = Auth::user();
        // Pastikan dia punya profil mahasiswa (jaga-jaga)
        if (!$user->studentProfile) {
            return back()->with('error', 'Data profil mahasiswa tidak ditemukan.');
        }
        $studentProfile = $user->studentProfile;

        // Ubah input token jadi huruf besar semua biar konsisten
        $inputToken = strtoupper($request->token);

        // 2. Cari Sesi Berdasarkan Token di Database
        // Kita eager load 'course' karena butuh datanya nanti
        $session = AttendanceSession::where('session_token', $inputToken)
            ->with('course')
            ->first();

        // --- VALIDASI BERLAPIS (KEAMANAN) ---

        // A. Cek apakah token ditemukan?
        if (!$session) {
            // Gunakan withErrors untuk mengirim error spesifik ke field input
            return back()->withErrors(['token' => 'Token tidak valid atau sesi tidak ditemukan.'])->withInput();
        }

        // B. Cek apakah status sesi OPEN?
        if ($session->status !== 'open') {
            return back()->with('error', 'Sesi presensi ini sudah ditutup atau belum dibuka oleh dosen.');
        }

        // C. Cek Waktu (Apakah sekarang masih dalam rentang jam sesi?)
        $now = Carbon::now();
        // Menggunakan accessor start_datetime dan end_datetime yang sudah kita buat di Model sebelumnya
        if (!$now->between($session->start_datetime, $session->end_datetime)) {
            return back()->with('error', 'Waktu presensi untuk sesi ini sudah berakhir.');
        }

        // D. CEK KRUSIAL: Apakah mahasiswa ini MENGAMBIL mata kuliah tersebut?
        // Ini mencegah mahasiswa dari kelas lain absen sembarangan.
        // Kita cek lewat relasi many-to-many di studentProfile
        $isEnrolled = $studentProfile->courses()
            ->where('courses.id', $session->course_id)
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'Anda tidak terdaftar di mata kuliah ini (' . $session->course->course_name . '). Presensi ditolak.');
        }

        // E. Cek apakah mahasiswa sudah pernah absen di sesi ini sebelumnya? (Mencegah double input)
        $alreadyCheckedIn = AttendanceRecord::where('session_id', $session->id)
            ->where('student_id', $studentProfile->id)
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('warning', 'Anda sudah berhasil melakukan presensi pada sesi ini sebelumnya.');
        }

        // --- SEMUA CEK LOLOS, SIMPAN DATA ---

        try {
            // Simpan rekam kehadiran
            AttendanceRecord::create([
                'session_id' => $session->id,
                'student_id' => $studentProfile->id,
                'status' => 'present', // Untuk sementara anggap 'hadir' jika berhasil input token
                'submission_time' => $now,
                // Nanti bisa ditambah photo_path atau lokasi jika fitur sudah ada
            ]);

            // Redirect ke dashboard dengan pesan sukses
            return redirect()->route('student.dashboard')->with('success', 'Presensi berhasil! Kehadiran Anda di mata kuliah ' . $session->course->course_name . ' telah tercatat.');
        } catch (\Exception $e) {
            // Tangani jika ada error database tak terduga
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data. Silakan coba lagi.');
        }
    }
}
