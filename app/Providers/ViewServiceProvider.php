<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Models\Course;
use App\Models\Location;
use Carbon\Carbon;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('admin.dashboard', function ($view) {
            // 1. Data Stats (Sudah ada)
            $stats = [
                'total_students' => User::where('role', 'student')->count(),
                'total_lecturers' => User::where('role', 'lecturer')->count(),
                'total_courses' => Course::count(),
                'total_locations' => Location::count(),
            ];

            // 2. Data Chart (PINDAHKAN LOGIKA DARI CONTROLLER KE SINI)
            $currentYear = Carbon::now()->year;
            $monthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $newUsersData = array_fill(0, 12, 0);
            $attendanceActivityData = array_fill(0, 12, 0);

            // Metrik 1: Registrasi Pengguna Baru
            $newUsersPerMonth = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

            for ($i = 1; $i <= 12; $i++) {
                $newUsersData[$i - 1] = $newUsersPerMonth[$i] ?? 0;
            }

            // Metrik 2: Total Aktivitas Absensi
            $attendancePerMonth = AttendanceRecord::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

            for ($i = 1; $i <= 12; $i++) {
                $attendanceActivityData[$i - 1] = $attendancePerMonth[$i] ?? 0;
            }

            // 3. Kirim SEMUA variabel ke view
            $view->with('stats', $stats)
                ->with('monthsLabels', $monthsLabels)
                ->with('newUsersData', $newUsersData)
                ->with('attendanceActivityData', $attendanceActivityData);
        });
    }
}
