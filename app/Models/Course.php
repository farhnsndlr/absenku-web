<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'start_time',
        'end_time',
        'session_type',
        'location_id',
        'description',
        'academic_year',
        'lecturer_id',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Relasi ke Dosen Pengampu.
     * Karena lecturer_id di tabel courses merujuk ke tabel users,
     * maka relasi ini harus ke model User.
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Relasi ke Sesi Presensi (One-to-Many).
     */
    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function students()
    {
        return $this->belongsToMany(
            StudentProfile::class,
            'course_enrollments',
            'course_id',
            'student_profile_id'
        )->withTimestamps()->withPivot('class_name');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id');
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function location()
{
    return $this->belongsTo(Location::class, 'location_id');
}
}
