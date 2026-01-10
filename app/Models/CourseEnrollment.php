<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $table = 'course_enrollments';

    protected $fillable = [
        'course_id',
        'student_profile_id',
    ];

    // Relasi ke mata kuliah.
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Relasi ke profil mahasiswa.
    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}
