<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'npm',
        'class_name',
        'full_name',
        'phone_number',
    ];

    // Relasi ke user.
    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    // Relasi ke catatan presensi.
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    // Relasi ke mata kuliah yang diambil.
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'student_profile_id', 'course_id')
            ->withTimestamps()
            ->withPivot('class_name');
    }

    // Normalisasi nama kelas saat disimpan.
    public function setClassNameAttribute($value)
    {
        $this->attributes['class_name'] = $value !== null ? strtoupper((string) $value) : null;
    }
}
