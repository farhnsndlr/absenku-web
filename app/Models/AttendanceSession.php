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
        'class_name',
        'lecturer_id',
        'session_date',
        'start_time',
        'end_time',
        'learning_type',
        'location_id',
        'status',
    ];

    // Casts penting agar kolom tanggal/waktu dibaca sebagai objek Carbon (DateTime) oleh Laravel
    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relasi ke Mata Kuliah (Belongs-to)
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Relasi ke Lokasi (Belongs-to, Nullable)
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    // Relasi ke Rekam Presensi Mahasiswa (One-to-Many)
    public function records()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    public function getIsOpenForAttendanceAttribute()
    {
        if ($this->status !== 'open') {
            return false;
        }

        $now = Carbon::now();

        $sessionStart = $this->session_date->copy()->setTimeFrom($this->start_time);
        $sessionEnd = $this->session_date->copy()->setTimeFrom($this->end_time);

        return $now->between($sessionStart, $sessionEnd);
    }
}
