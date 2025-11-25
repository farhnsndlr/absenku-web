<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\LecturerProfile;
use App\Models\Location;
use App\Models\Course;
use App\Models\AttendanceSession;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\AttendanceRecord;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ---------------------------------------------------
        // 1. Buat Akun Admin
        // ---------------------------------------------------
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@absenku.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        $this->command->info('✅ User Admin created.');

        // ---------------------------------------------------
        // 2. Lokasi Kampus
        // ---------------------------------------------------
        $location = Location::create([
            'location_name' => 'Kampus Utama - Gedung A',
            'latitude' => -6.376581274282782,
            'longitude' => 106.88655029256103,
            'radius_meters' => 100,
        ]);
        $this->command->info('✅ Location created.');

        // ---------------------------------------------------
        // 3. Buat Dosen & Mata Kuliah
        // ---------------------------------------------------
        $lecturerProfile = LecturerProfile::create([
            'nid' => '198501012010011001',
            'full_name' => 'Dr. Budi Santoso, M.Kom.',
            'phone_number' => '081298765432',
        ]);

        $lecturerUser = $lecturerProfile->user()->create([
            'name' => 'Budi Santoso',
            'email' => 'dosen@absenku.com',
            'password' => Hash::make('password123'),
            'role' => 'lecturer',
        ]);

        $course = Course::create([
            'course_code' => 'IF401',
            'course_name' => 'Pemrograman Web Lanjut',
            'start_time' => Carbon::createFromTime(8, 0, 0),
            'end_time' => Carbon::createFromTime(10, 0, 0),

            // FIX: lecturer_id pakai ID user dosen
            'lecturer_id' => $lecturerUser->id,
        ]);
        $this->command->info('✅ Lecturer & Course created.');

        // ---------------------------------------------------
        // 4. Buat Mahasiswa & Enroll
        // ---------------------------------------------------
        $studentProfile = StudentProfile::create([
            'npm' => '202310001',
            'full_name' => 'Ahmad Farhan',
            'phone_number' => '081312345678',
        ]);

        $studentUser = $studentProfile->user()->create([
            'name' => 'Farhan Mhs',
            'email' => 'mahasiswa@absenku.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Enroll ke mata kuliah
        $studentProfile->courses()->attach($course->id);
        $this->command->info('✅ Student created & enrolled to course.');

        // ---------------------------------------------------
        // 5. Buat Sesi Kehadiran Hari Ini
        // ---------------------------------------------------
        $attendanceSession = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturerUser->id, // <= WAJIB FIX
            'session_date' => Carbon::today(),
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->addHours(2),
            'learning_type' => 'offline',
            'location_id' => $location->id,
            'status' => 'open',
        ]);
        $this->command->info('✅ Active Session for TODAY created.');

        // ---------------------------------------------------
        // 6. Dummy Rekam Absensi
        // ---------------------------------------------------
        AttendanceRecord::create([
            'session_id' => $attendanceSession->id,
            'student_id' => $studentProfile->id,
            'status' => 'present',
            'submission_time' => Carbon::now()->subMinutes(30),
            'learning_type' => 'onsite',
        ]);

        $this->command->info('✅ Dummy Attendance Record created.');
    }
}
