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
        'start_time',
        'end_time',
        'lecturer_id', // Ini merujuk ke ID di tabel users (role lecturer)
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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
        return $this->hasMany(AttendanceSession::class, 'course_id');
    }

    /**
     * Relasi ke Mahasiswa yang mengambil matkul ini (Many-to-Many).
     * Tabel pivot menghubungkan course_id dan student_profile_id (ID dari tabel student_profiles).
     * PERBAIKAN: Mengganti 'student_id' menjadi 'student_profile_id' agar sesuai dengan migrasi database.
     */
    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'course_enrollments',
            'course_id',
            'student_profile_id'
        )->withTimestamps();
    }
}
