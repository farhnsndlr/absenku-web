<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\StudentProfile;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'student_id',
        'submission_time',
        'photo_path',
        'supporting_document_path',
        'location_maps',
        'status',
        'learning_type',
    ];

    protected $casts = [
        'submission_time' => 'datetime',
    ];

    // Relasi ke sesi presensi.
    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    // Relasi ke user (mahasiswa).
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relasi ke profil mahasiswa.
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }
}
