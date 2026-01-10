<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lecturer_id',
        'class_name',
        'session_date',
        'start_time',
        'end_time',
        'learning_type',
        'location_id',
        'status',
        'description',
        'session_token',
        'session_token_expires_at',
        'late_tolerance_minutes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'session_token_expires_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',

    ];

    // Relasi ke mata kuliah.
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Relasi ke lokasi.
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    // Relasi ke catatan presensi.
    public function records()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    // Relasi ke catatan presensi.
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    // Accessor waktu mulai lengkap.
    public function getStartDateTimeAttribute()
    {
        $rawDate = $this->getAttributes()['session_date'];
        $rawTime = $this->getAttributes()['start_time'];

        $dateOnly = substr($rawDate, 0, 10);

        if (strlen($rawTime) > 8) {
            $timeOnly = substr($rawTime, 11, 8);
        } else {
            $timeOnly = $rawTime;
        }

        return Carbon::parse($dateOnly . ' ' . $timeOnly);
    }

    // Accessor waktu selesai lengkap.
    public function getEndDateTimeAttribute()
    {
        $rawDate = $this->getAttributes()['session_date'];
        $rawTime = $this->getAttributes()['end_time'];

        $dateOnly = substr($rawDate, 0, 10);

        if (strlen($rawTime) > 8) {
            $timeOnly = substr($rawTime, 11, 8);
        } else {
            $timeOnly = $rawTime;
        }

        return Carbon::parse($dateOnly . ' ' . $timeOnly);
    }

    // Accessor format jam mulai.
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_date_time->format('H:i');
    }

    // Accessor format jam selesai.
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_date_time->format('H:i');
    }

    // Accessor topik sesi.
    public function getTopicAttribute()
    {
        return $this->description;
    }

    // Mutator topik sesi.
    public function setTopicAttribute($value)
    {
        $this->attributes['description'] = $value;
    }

    // Normalisasi nama kelas saat disimpan.
    public function setClassNameAttribute($value)
    {
        $this->attributes['class_name'] = $value !== null ? strtoupper((string) $value) : null;
    }

    // Accessor status sesi masih bisa diabsen.
    public function getIsOpenForAttendanceAttribute()
    {
        if ($this->status !== 'open') return false;

        $now = Carbon::now();
        return $now->between($this->start_time, $this->end_time);
    }

    // Accessor status token masih aktif.
    public function getIsTokenActiveAttribute()
    {
        if (!$this->session_token_expires_at) return false;
        return now()->lessThanOrEqualTo($this->session_token_expires_at);
    }
}
