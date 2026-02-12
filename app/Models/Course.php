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

    // Relasi ke dosen pengampu.
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    // Relasi ke sesi.
    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    // Relasi ke mahasiswa peserta.
    public function students()
    {
        return $this->belongsToMany(
            StudentProfile::class,
            'course_enrollments',
            'course_id',
            'student_profile_id'
        )->withTimestamps()->withPivot('class_name');
    }

    // Relasi ke enrollment.
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id');
    }

    // Relasi ke sesi presensi.
    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    // Relasi ke lokasi default.
    public function location()
{
    return $this->belongsTo(Location::class, 'location_id');
}
}
