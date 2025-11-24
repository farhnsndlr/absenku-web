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

    // Relasi ke Mata Kuliah yang diambil (Many-to-Many via course_enrollments)
    // INI PENTING YANG KAMU INGETIN TADI!
    // app/Models/StudentProfile.php

    public function courses()
    {
        // Parameter ke-2 WAJIB diisi dengan nama tabel pivot kita: 'course_enrollments'
        return $this->belongsToMany(Course::class, 'course_enrollments', 'student_profile_id', 'course_id');
    }
}
