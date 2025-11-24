<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'submission_time' => 'datetime', // Casting agar bisa menggunakan format() di Exporter
    ];

    // Relasi ke Sesi (Belongs-to)
    public function session(): BelongsTo // Tambahkan type hint
    {
        // Parameter kedua harus sesuai nama kolom FK di database
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    // Relasi ke Mahasiswa (Belongs-to)
    public function student(): BelongsTo
    {
        // Menggunakan student_id sebagai FK dan merujuk ke StudentProfile
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }
}
