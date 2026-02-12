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
    // Menyuntikkan data global ke view tertentu.
    public function boot(): void
    {
        View::composer('admin.dashboard', function ($view) {
            $stats = [
                'total_students' => User::where('role', 'student')->count(),
                'total_lecturers' => User::where('role', 'lecturer')->count(),
                'total_courses' => Course::count(),
                'total_locations' => Location::count(),
            ];

            $currentYear = Carbon::now()->year;
            $monthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $newUsersData = array_fill(0, 12, 0);
            $attendanceActivityData = array_fill(0, 12, 0);

            $newUsersPerMonth = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

            for ($i = 1; $i <= 12; $i++) {
                $newUsersData[$i - 1] = $newUsersPerMonth[$i] ?? 0;
            }

            $attendancePerMonth = AttendanceRecord::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

            for ($i = 1; $i <= 12; $i++) {
                $attendanceActivityData[$i - 1] = $attendancePerMonth[$i] ?? 0;
            }

            $view->with('stats', $stats)
                ->with('monthsLabels', $monthsLabels)
                ->with('newUsersData', $newUsersData)
                ->with('attendanceActivityData', $attendanceActivityData);
        });
    }
}
