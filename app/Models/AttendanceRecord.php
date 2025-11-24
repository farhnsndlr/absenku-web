<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'student_id',
        'submission_time',
        'photo_path', // URL Cloudinary
        'location_maps', // Lat,Long string
        'status', // 'present', 'late', dll.
        'learning_type', // 'online' atau 'onsite'
    ];

    // Casts penting
    protected $casts = [
        'submission_time' => 'datetime',
    ];

    // Relasi ke Sesi (Belongs-to)
    public function session()
    {
        // Parameter kedua harus sesuai nama kolom FK di database
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    // Relasi ke Mahasiswa (Belongs-to)
    public function student()
    {
        // Ini merujuk ke model StudentProfile, bukan User
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }
}
