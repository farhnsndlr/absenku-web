<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Location;
use App\Models\AttendanceRecord; // Jangan lupa import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Jangan lupa import DB facade
use Carbon\Carbon; // Jangan lupa import Carbon

class AdminDashboardController extends Controller
{
    public function index()
    {
        // --- 1. Stats Cards Data (Sudah ada sebelumnya) ---
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_lecturers' => User::where('role', 'lecturer')->count(),
            'total_courses' => Course::count(),
            'total_locations' => Location::count(),
        ];

        // --- 2. Chart Data: Persiapan ---
        // Kita akan mengambil data tahun ini
        $currentYear = Carbon::now()->year;
        // Label bulan statis untuk sumbu X
        $monthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        // Inisialisasi array data dengan nilai 0 untuk 12 bulan
        $newUsersData = array_fill(0, 12, 0);
        $attendanceActivityData = array_fill(0, 12, 0);


        // --- 3. Chart Data: Metrik 1 - Registrasi Pengguna Baru per Bulan ---
        // Menggunakan query raw untuk grouping berdasarkan bulan
        $newUsersPerMonth = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('count', 'month') // Menghasilkan array [bulan => jumlah]
            ->toArray();

        // Mengisi array data final berdasarkan hasil query
        // Loop 1-12 (Januari-Desember)
        for ($i = 1; $i <= 12; $i++) {
            // Jika bulan $i ada datanya di query, pakai datanya, jika tidak pakai 0
            $newUsersData[$i - 1] = $newUsersPerMonth[$i] ?? 0;
        }


        // --- 4. Chart Data: Metrik 2 - Total Aktivitas Absensi per Bulan ---
        // Menghitung jumlah record absensi yang dibuat per bulan
        $attendancePerMonth = AttendanceRecord::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Mengisi array data final
        for ($i = 1; $i <= 12; $i++) {
            $attendanceActivityData[$i - 1] = $attendancePerMonth[$i] ?? 0;
        }

        // --- 5. Kirim Data ke View ---
        return view('admin.dashboard', compact(
            'stats',
            // Kirim data chart terpisah agar rapi
            'monthsLabels',
            'newUsersData',
            'attendanceActivityData'
        ));
    }
}
