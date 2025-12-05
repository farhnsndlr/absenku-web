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
    ];

    protected $casts = [
        'session_date' => 'date',
        'session_token_expires_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',

    ];

    // Relationships ===========================
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    public function getStartDateTimeAttribute()
    {
        $rawDate = $this->getAttributes()['session_date'];
        $rawTime = $this->getAttributes()['start_time'];

        // Extract date part (Y-m-d)
        $dateOnly = substr($rawDate, 0, 10);

        // Extract time part - handle jika sudah datetime lengkap
        if (strlen($rawTime) > 8) {
            $timeOnly = substr($rawTime, 11, 8);
        } else {
            $timeOnly = $rawTime;
        }

        return Carbon::parse($dateOnly . ' ' . $timeOnly);
    }

    public function getEndDateTimeAttribute()
    {
        $rawDate = $this->getAttributes()['session_date'];
        $rawTime = $this->getAttributes()['end_time'];

        // Extract date part (Y-m-d)
        $dateOnly = substr($rawDate, 0, 10);

        // Extract time part - handle jika sudah datetime lengkap
        if (strlen($rawTime) > 8) {
            $timeOnly = substr($rawTime, 11, 8);
        } else {
            $timeOnly = $rawTime;
        }

        return Carbon::parse($dateOnly . ' ' . $timeOnly);
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_date_time->format('H:i');
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_date_time->format('H:i');
    }

    // Attribute: apakah sesi masih bisa diabsen?
    public function getIsOpenForAttendanceAttribute()
    {
        if ($this->status !== 'open') return false;

        $now = Carbon::now();
        return $now->between($this->start_time, $this->end_time);
    }

    public function getIsTokenActiveAttribute()
    {
        if (!$this->session_token_expires_at) return false;
        return now()->lessThanOrEqualTo($this->session_token_expires_at);
    }
}
