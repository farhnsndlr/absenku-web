<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\LecturerProfile;
use App\Models\Location;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');

        // ===================================================================
        // 1. ADMIN ACCOUNT
        // ===================================================================
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@absenku.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        $this->command->info('✅ Admin account created.');

        // ===================================================================
        // 2. LOCATIONS (Multiple Locations)
        // ===================================================================
        $locations = [
            [
                'location_name' => 'Gedung A - Lantai 1',
                'latitude' => -6.376581274282782,
                'longitude' => 106.88655029256103,
                'radius_meters' => 100,
            ],
            [
                'location_name' => 'Gedung B - Lab Komputer',
                'latitude' => -6.377123,
                'longitude' => 106.887234,
                'radius_meters' => 50,
            ],
            [
                'location_name' => 'Gedung C - Ruang Kuliah Besar',
                'latitude' => -6.375987,
                'longitude' => 106.886123,
                'radius_meters' => 75,
            ],
        ];

        foreach ($locations as $loc) {
            Location::create($loc);
        }
        $this->command->info('✅ ' . count($locations) . ' locations created.');

        // ===================================================================
        // 3. LECTURERS (3 Dosen)
        // ===================================================================
        $lecturers = [
            [
                'nid' => '198501012010011001',
                'full_name' => 'Dr. Budi Santoso, M.Kom.',
                'phone_number' => '081298765432',
                'email' => 'dosen@absenku.com',
                'name' => 'Budi Santoso',
            ],
            [
                'nid' => '198706152011012002',
                'full_name' => 'Prof. Siti Rahmawati, Ph.D.',
                'phone_number' => '081345678901',
                'email' => 'siti.rahmawati@absenku.com',
                'name' => 'Siti Rahmawati',
            ],
            [
                'nid' => '199002282015011003',
                'full_name' => 'Ir. Andi Wijaya, M.T.',
                'phone_number' => '081456789012',
                'email' => 'andi.wijaya@absenku.com',
                'name' => 'Andi Wijaya',
            ],
        ];

        $lecturerUsers = [];
        foreach ($lecturers as $lec) {
            $profile = LecturerProfile::create([
                'nid' => $lec['nid'],
                'full_name' => $lec['full_name'],
                'phone_number' => $lec['phone_number'],
            ]);

            $lecturerUsers[] = $profile->user()->create([
                'name' => $lec['name'],
                'email' => $lec['email'],
                'password' => Hash::make('password123'),
                'role' => 'lecturer',
            ]);
        }
        $this->command->info('✅ ' . count($lecturers) . ' lecturers created.');

        // ===================================================================
        // 4. COURSES (5 Mata Kuliah)
        // ===================================================================
        $courses = [
            [
                'course_code' => 'IF401',
                'course_name' => 'Pemrograman Web Lanjut',
                'lecturer_id' => $lecturerUsers[0]->id,
            ],
            [
                'course_code' => 'IF402',
                'course_name' => 'Basis Data Lanjut',
                'lecturer_id' => $lecturerUsers[0]->id,
            ],
            [
                'course_code' => 'IF403',
                'course_name' => 'Keamanan Sistem Informasi',
                'lecturer_id' => $lecturerUsers[1]->id,
            ],
            [
                'course_code' => 'IF404',
                'course_name' => 'Kecerdasan Buatan',
                'lecturer_id' => $lecturerUsers[1]->id,
            ],
            [
                'course_code' => 'IF405',
                'course_name' => 'Jaringan Komputer',
                'lecturer_id' => $lecturerUsers[2]->id,
            ],
        ];

        $courseModels = [];
        foreach ($courses as $c) {
            $courseModels[] = Course::create($c);
        }
        $this->command->info('✅ ' . count($courses) . ' courses created.');

        // ===================================================================
        // 5. STUDENTS (10 Mahasiswa)
        // ===================================================================
        $students = [
            ['npm' => '202310001', 'full_name' => 'Farhan Maulana', 'phone' => '081312345678', 'email' => 'mahasiswa@absenku.com', 'name' => 'Farhan Mhs', 'class' => '3KA15'],
            ['npm' => '202310002', 'full_name' => 'Siti Nurhaliza', 'phone' => '081312345679', 'email' => 'siti.nurhaliza@student.com', 'name' => 'Siti', 'class' => '3KA15'],
            ['npm' => '202310003', 'full_name' => 'Budi Hartono', 'phone' => '081312345680', 'email' => 'budi.hartono@student.com', 'name' => 'Budi', 'class' => '3KA15'],
            ['npm' => '202310004', 'full_name' => 'Dewi Lestari', 'phone' => '081312345681', 'email' => 'dewi.lestari@student.com', 'name' => 'Dewi', 'class' => '3KA15'],
            ['npm' => '202310005', 'full_name' => 'Rizki Ramadhan', 'phone' => '081312345682', 'email' => 'rizki.ramadhan@student.com', 'name' => 'Rizki', 'class' => '3KA16'],
            ['npm' => '202310006', 'full_name' => 'Putri Amelia', 'phone' => '081312345683', 'email' => 'putri.amelia@student.com', 'name' => 'Putri', 'class' => '3KA16'],
            ['npm' => '202310007', 'full_name' => 'Ahmad Fauzi', 'phone' => '081312345684', 'email' => 'ahmad.fauzi@student.com', 'name' => 'Ahmad', 'class' => '3KA16'],
            ['npm' => '202310008', 'full_name' => 'Rina Susanti', 'phone' => '081312345685', 'email' => 'rina.susanti@student.com', 'name' => 'Rina', 'class' => '3KA16'],
            ['npm' => '202310009', 'full_name' => 'Dani Pratama', 'phone' => '081312345686', 'email' => 'dani.pratama@student.com', 'name' => 'Dani', 'class' => '3KA17'],
            ['npm' => '202310010', 'full_name' => 'Lisa Andriani', 'phone' => '081312345687', 'email' => 'lisa.andriani@student.com', 'name' => 'Lisa', 'class' => '3KA17'],
        ];

        $studentProfiles = [];
        foreach ($students as $s) {
            $profile = StudentProfile::create([
                'npm' => $s['npm'],
                'full_name' => $s['full_name'],
                'phone_number' => $s['phone'],
            ]);

            $profile->user()->create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]);

            $studentProfiles[] = ['profile' => $profile, 'class' => $s['class']];
        }
        $this->command->info('✅ ' . count($students) . ' students created.');

        // ===================================================================
        // 6. COURSE ENROLLMENTS (Mahasiswa mengambil mata kuliah)
        // ===================================================================
        // Mahasiswa kelas 3KA15 (4 orang) mengambil IF401 dan IF402
        // Mahasiswa kelas 3KA16 (4 orang) mengambil IF403 dan IF404
        // Mahasiswa kelas 3KA17 (2 orang) mengambil IF405

        // Kelas 3KA15
        for ($i = 0; $i < 4; $i++) {
            $studentProfiles[$i]['profile']->courses()->attach($courseModels[0]->id, ['class_name' => '3KA15']); // IF401
            $studentProfiles[$i]['profile']->courses()->attach($courseModels[1]->id, ['class_name' => '3KA15']); // IF402
        }

        // Kelas 3KA16
        for ($i = 4; $i < 8; $i++) {
            $studentProfiles[$i]['profile']->courses()->attach($courseModels[2]->id, ['class_name' => '3KA16']); // IF403
            $studentProfiles[$i]['profile']->courses()->attach($courseModels[3]->id, ['class_name' => '3KA16']); // IF404
        }

        // Kelas 3KA17
        for ($i = 8; $i < 10; $i++) {
            $studentProfiles[$i]['profile']->courses()->attach($courseModels[4]->id, ['class_name' => '3KA17']); // IF405
        }

        $this->command->info('✅ Course enrollments completed.');

        // ===================================================================
        // 7. ATTENDANCE SESSIONS (Berbagai Skenario)
        // ===================================================================
        $now = Carbon::now();
        $today = Carbon::today();
        $locationIds = Location::pluck('id')->toArray();

        $sessions = [
            // === SESI HARI INI ===

            // 1. Sesi SEDANG BERLANGSUNG (IF401 - 3KA15)
            [
                'course_id' => $courseModels[0]->id,
                 'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA15',
                'session_date' => $today,
                'start_time' => $now->copy()->subMinutes(30),
                'end_time' => $now->copy()->addHour(),
                'learning_type' => 'offline',
                'location_id' => $locationIds[0],
                'status' => 'open',
                'description' => 'Sesi aktif - Testing Check-In',
            ],

            // 2. Sesi AKAN DATANG (IF402 - 3KA15)
            [
                'course_id' => $courseModels[1]->id,
                'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA15',
                'session_date' => $today,
                'start_time' => $now->copy()->addHours(2),
                'end_time' => $now->copy()->addHours(4),
                'learning_type' => 'online',
                'location_id' => null,
                'status' => 'open',
                'description' => 'Sesi kuliah online sore ini',
            ],

            // 3. Sesi SUDAH BERAKHIR (IF401 - 3KA15)
            [
                'course_id' => $courseModels[0]->id,
                'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA15',
                'session_date' => $today,
                'start_time' => $now->copy()->subHours(3),
                'end_time' => $now->copy()->subHours(1),
                'learning_type' => 'offline',
                'location_id' => $locationIds[0],
                'status' => 'closed',
                'description' => 'Sesi pagi yang sudah selesai',
            ],

            // 4. Sesi SEDANG BERLANGSUNG (IF403 - 3KA16)
            [
                'course_id' => $courseModels[2]->id,
                'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA16',
                'session_date' => $today,
                'start_time' => $now->copy()->subMinutes(20),
                'end_time' => $now->copy()->addMinutes(70),
                'learning_type' => 'offline',
                'location_id' => $locationIds[1],
                'status' => 'open',
                'description' => 'Lab Komputer - Praktikum Keamanan Sistem',
            ],

            // 5. Sesi ONLINE AKTIF (IF404 - 3KA16)
            [
                'course_id' => $courseModels[3]->id,
                'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA16',
                'session_date' => $today,
                'start_time' => $now->copy()->subMinutes(10),
                'end_time' => $now->copy()->addMinutes(80),
                'learning_type' => 'online',
                'location_id' => null,
                'status' => 'open',
                'description' => 'Kuliah online via Zoom',
            ],

            // === SESI KEMARIN (Untuk testing riwayat) ===

            // 6. Sesi kemarin (IF401 - 3KA15) - CLOSED
            [
                'course_id' => $courseModels[0]->id,
                'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA15',
                'session_date' => $today->copy()->subDay(),
                'start_time' => $today->copy()->subDay()->setTime(8, 0),
                'end_time' => $today->copy()->subDay()->setTime(10, 0),
                'learning_type' => 'offline',
                'location_id' => $locationIds[0],
                'status' => 'closed',
                'description' => 'Pertemuan minggu lalu',
            ],

            // 7. Sesi 2 hari lalu (IF402 - 3KA15) - CLOSED
            [
                'course_id' => $courseModels[1]->id,
                'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA15',
                'session_date' => $today->copy()->subDays(2),
                'start_time' => $today->copy()->subDays(2)->setTime(13, 0),
                'end_time' => $today->copy()->subDays(2)->setTime(15, 0),
                'learning_type' => 'online',
                'location_id' => null,
                'status' => 'closed',
                'description' => 'Kuliah basis data',
            ],

            // 8. Sesi minggu lalu (IF405 - 3KA17)
            [
                'course_id' => $courseModels[4]->id,
                'lecturer_id' => $lecturerUsers[0]->id,
                'class_name' => '3KA17',
                'session_date' => $today->copy()->subDays(7),
                'start_time' => $today->copy()->subDays(7)->setTime(10, 0),
                'end_time' => $today->copy()->subDays(7)->setTime(12, 0),
                'learning_type' => 'offline',
                'location_id' => $locationIds[2],
                'status' => 'closed',
                'description' => 'Praktikum Jaringan',
            ],
        ];

        $sessionModels = [];
        foreach ($sessions as $sess) {
            $sessionModels[] = AttendanceSession::create($sess);
        }
        $this->command->info('✅ ' . count($sessions) . ' attendance sessions created.');

        // ===================================================================
        // 8. ATTENDANCE RECORDS (Rekam absensi mahasiswa)
        // ===================================================================
        $records = [];

        // Sesi 1 (Sedang berlangsung IF401-3KA15): 2 mahasiswa sudah check-in
        $records[] = [
            'session_id' => $sessionModels[0]->id,
            'student_id' => $studentProfiles[0]['profile']->id, // Farhan
            'status' => 'present',
            'submission_time' => $now->copy()->subMinutes(25),
            'learning_type' => 'offline',
        ];
        $records[] = [
            'session_id' => $sessionModels[0]->id,
            'student_id' => $studentProfiles[1]['profile']->id, // Siti
            'status' => 'late',
            'submission_time' => $now->copy()->subMinutes(10),
            'learning_type' => 'offline',
        ];

        // Sesi 3 (Sudah berakhir IF401-3KA15): 3 mahasiswa hadir, 1 absent
        $records[] = [
            'session_id' => $sessionModels[2]->id,
            'student_id' => $studentProfiles[0]['profile']->id,
            'status' => 'present',
            'submission_time' => $sessionModels[2]->start_time->copy()->addMinutes(5),
            'learning_type' => 'offline',
        ];
        $records[] = [
            'session_id' => $sessionModels[2]->id,
            'student_id' => $studentProfiles[1]['profile']->id,
            'status' => 'present',
            'submission_time' => $sessionModels[2]->start_time->copy()->addMinutes(3),
            'learning_type' => 'offline',
        ];
        $records[] = [
            'session_id' => $sessionModels[2]->id,
            'student_id' => $studentProfiles[2]['profile']->id,
            'status' => 'late',
            'submission_time' => $sessionModels[2]->start_time->copy()->addMinutes(20),
            'learning_type' => 'offline',
        ];

        // Sesi 4 (Sedang berlangsung IF403-3KA16): 1 mahasiswa sudah check-in
        $records[] = [
            'session_id' => $sessionModels[3]->id,
            'student_id' => $studentProfiles[4]['profile']->id, // Rizki (3KA16)
            'status' => 'present',
            'submission_time' => $now->copy()->subMinutes(15),
            'learning_type' => 'offline',
        ];

        // Sesi 6 (Kemarin IF401-3KA15): Semua mahasiswa hadir
        foreach (array_slice($studentProfiles, 0, 4) as $sp) {
            $records[] = [
                'session_id' => $sessionModels[5]->id,
                'student_id' => $sp['profile']->id,
                'status' => 'present',
                'submission_time' => $sessionModels[5]->start_time->copy()->addMinutes(rand(2, 10)),
                'learning_type' => 'offline',
            ];
        }

        // Sesi 7 (2 hari lalu IF402-3KA15): Mixed attendance
        $records[] = [
            'session_id' => $sessionModels[6]->id,
            'student_id' => $studentProfiles[0]['profile']->id,
            'status' => 'present',
            'submission_time' => $sessionModels[6]->start_time->copy()->addMinutes(5),
            'learning_type' => 'online',
        ];
        $records[] = [
            'session_id' => $sessionModels[6]->id,
            'student_id' => $studentProfiles[2]['profile']->id,
            'status' => 'permit',
            'submission_time' => $sessionModels[6]->start_time->copy()->addMinutes(30),
            'learning_type' => 'online',
        ];

        foreach ($records as $rec) {
            AttendanceRecord::create($rec);
        }
        $this->command->info('✅ ' . count($records) . ' attendance records created.');

        // ===================================================================
        // SUMMARY
        // ===================================================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('🎉 DATABASE SEEDING COMPLETED!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📋 Summary:');
        $this->command->info('   - 1 Admin account');
        $this->command->info('   - 3 Lecturers');
        $this->command->info('   - 5 Courses');
        $this->command->info('   - 10 Students (3 classes: 3KA15, 3KA16, 3KA17)');
        $this->command->info('   - 3 Locations');
        $this->command->info('   - 8 Attendance Sessions (active, upcoming, closed)');
        $this->command->info('   - ' . count($records) . ' Attendance Records');
        $this->command->info('');
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('   Admin:     admin@absenku.com / password123');
        $this->command->info('   Lecturer:  dosen@absenku.com / password123');
        $this->command->info('   Student:   mahasiswa@absenku.com / password123');
        $this->command->info('');
        $this->command->info('✨ You can now test all features!');
        $this->command->info('========================================');
    }
}
