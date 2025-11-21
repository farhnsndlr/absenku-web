<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'course_time',
        'lecturer_id',
    ];

    // Relasi ke Dosen Pengampu (Belongs-to)
    public function lecturer()
    {
        return $this->belongsTo(LecturerProfile::class, 'lecturer_id');
    }

    // Relasi ke Sesi Presensi (One-to-Many)
    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class, 'course_id');
    }

    // Relasi ke Mahasiswa yang mengambil matkul ini (Many-to-Many)
    public function students()
    {
        return $this->belongsToMany(StudentProfile::class, 'course_enrollments', 'course_id', 'student_id')
            ->withTimestamps();
    }
}
