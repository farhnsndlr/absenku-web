<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\Course;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Mengambil data statistik sederhana untuk Dashboard
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_lecturers' => User::where('role', 'lecturer')->count(),
            'total_courses' => Course::count(),
            'total_locations' => Location::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
