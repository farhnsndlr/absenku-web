<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'npm',
        'full_name',
        'phone_number',
    ];

    // Relasi ke User (One-to-One)
    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    // Relasi ke Catatan Presensi (One-to-Many)
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    /**
     * Relasi ke Mata Kuliah yang diambil (Many-to-Many via course_enrollments)
     */
    public function courses()
    {
        // Parameter ke-2 WAJIB diisi dengan nama tabel pivot kita: 'course_enrollments'
        // Parameter ke-3 dan ke-4 adalah nama foreign key di tabel pivot
        return $this->belongsToMany(Course::class, 'course_enrollments', 'student_profile_id', 'course_id')
            ->withTimestamps()
            // PENTING: Tambahkan baris ini agar kolom 'class_name' di tabel pivot ikut terambil
            ->withPivot('class_name');
    }
}
