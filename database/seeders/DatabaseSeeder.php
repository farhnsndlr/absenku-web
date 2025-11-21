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
            // profile_id null karena admin tidak punya profil khusus
        ]);
        $this->command->info('✅ User Admin created.');


        // ---------------------------------------------------
        // 2. Buat Lokasi Kampus Nyata
        // ---------------------------------------------------
        $location = Location::create([
            'location_name' => 'Kampus Utama - Gedung A',
            // # GANTI: Gunakan koordinat tempat kamu testing sekarang!
            // Tips: Buka Google Maps, klik kanan lokasi kamu, copy lat/long-nya.
            'latitude' => -6.376581274282782,
            'longitude' => 106.88655029256103,
            'radius_meters' => 100, // Radius 200 meter
        ]);
        $this->command->info('✅ Location created (Lat: ' . $location->latitude . ', Long: ' . $location->longitude . ')');


        // ---------------------------------------------------
        // 3. Buat Dosen & Mata Kuliah
        // ---------------------------------------------------
        // a. Buat Profil Dosen
        $lecturerProfile = LecturerProfile::create([
            'nid' => '198501012010011001',
            'full_name' => 'Dr. Budi Santoso, M.Kom.',
            'phone_number' => '081298765432',
        ]);

        // b. Buat User Dosen
        $lecturerUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'dosen@absenku.com',
            'password' => Hash::make('password123'),
            'role' => 'lecturer',
            'profile_id' => $lecturerProfile->id,
        ]);

        // c. Buat Mata Kuliah untuk Dosen ini
        $course = Course::create([
            'course_code' => 'IF401',
            'course_name' => 'Pemrograman Web Lanjut',
            'course_time' => 'Senin, 08:00 - 10:00',
            'lecturer_id' => $lecturerProfile->id,
        ]);
        $this->command->info('✅ Lecturer & Course created.');


        // ---------------------------------------------------
        // 4. Buat Mahasiswa & Enroll ke Mata Kuliah
        // ---------------------------------------------------
        // a. Buat Profil Mahasiswa
        $studentProfile = StudentProfile::create([
            'npm' => '202310001',
            'full_name' => 'Ahmad Farhan',
            'phone_number' => '081312345678',
        ]);

        // b. Buat User Mahasiswa
        User::create([
            'name' => 'Farhan Mhs',
            'email' => 'mahasiswa@absenku.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'profile_id' => $studentProfile->id,
        ]);

        // c. Enroll Mahasiswa ke Mata Kuliah tadi (PENTING!)
        // Menggunakan relasi belongsToMany yang sudah kita buat di Model
        $studentProfile->courses()->attach($course->id);

        $this->command->info('✅ Student created & enrolled to course.');


        // ---------------------------------------------------
        // 5. Buat Sesi Aktif HARI INI
        // ---------------------------------------------------
        // Sesi dibuat mulai dari 1 jam yang lalu sampai 2 jam ke depan dari sekarang.
        // Jadi saat kamu tes, sesi ini statusnya SEDANG BERLANGSUNG.
        AttendanceSession::create([
            'course_id' => $course->id,
            'session_date' => Carbon::today(), // Tanggal hari ini
            'start_time' => Carbon::now()->subHour(), // Mulai 1 jam lalu
            'end_time' => Carbon::now()->addHours(2), // Selesai 2 jam lagi
            'session_type' => 'offline',
            'location_id' => $location->id, // Menggunakan lokasi nyata tadi
            'description' => 'Sesi Pertemuan ke-1 (Testing Monolith)',
        ]);
        $this->command->info('✅ Active Session for TODAY created.');

        $this->command->info('---------------------------------------');
        $this->command->info('🎉 SEEDING COMPLETE! Database is ready.');
        $this->command->info('---------------------------------------');
        $this->command->info('Login Credentials (Password: password123):');
        $this->command->info('- Admin: admin@absenku.com');
        $this->command->info('- Dosen: dosen@absenku.com');
        $this->command->info('- Mahasiswa: mahasiswa@absenku.com');
    }
}
