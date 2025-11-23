<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        // 'profile_id', // <-- HAPUS INI DARI FILLABLE
        'profile_id', // Relasi polimorfik akan mengisi ini dan profile_type secara otomatis
        'profile_type',
    ];

    // Hapus relasi hasOne/belongsTo yang lama untuk lecturerProfile dan studentProfile
    // Ganti dengan relasi polimorfik 'profile'
    public function profile()
    {
        return $this->morphTo();
    }

    // Metode helper untuk akses langsung (opsional tapi bagus)
    public function lecturerProfile()
    {
        return $this->profile()->where('profile_type', LecturerProfile::class);
    }

    public function studentProfile()
    {
        return $this->profile()->where('profile_type', StudentProfile::class);
    }

    // Tambahkan metode ini untuk memastikan akses mudah ke profil yang tepat
    public function getLecturerProfileAttribute()
    {
        if ($this->profile_type === LecturerProfile::class) {
            return $this->profile;
        }
        return null;
    }

    public function getStudentProfileAttribute()
    {
        if ($this->profile_type === StudentProfile::class) {
            return $this->profile;
        }
        return null;
    }
}
