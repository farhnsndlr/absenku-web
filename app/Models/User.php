<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_id',
        'profile_type',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (isset($attributes['profile_photo_path']) && $attributes['profile_photo_path']) {
                    return Storage::url($attributes['profile_photo_path']);
                }

                return null;
            },
        );
    }

    // Relasi polimorfik ke profil.
    public function profile(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper query untuk profil dosen.
    public function lecturerProfile()
    {
        return $this->profile()->where('profile_type', LecturerProfile::class);
    }

    // Helper query untuk profil mahasiswa.
    public function studentProfile()
    {
        return $this->profile()->where('profile_type', StudentProfile::class);
    }

    // Accessor profil dosen.
    public function getLecturerProfileAttribute()
    {
        if ($this->profile_type === LecturerProfile::class) {
            return $this->profile;
        }
        return null;
    }

    // Accessor profil mahasiswa.
    public function getStudentProfileAttribute()
    {
        if ($this->profile_type === StudentProfile::class) {
            return $this->profile;
        }
        return null;
    }

    // Relasi mahasiswa pada mata kuliah.
    public function students()
    {
        return $this->belongsToMany(StudentProfile::class, 'course_enrollments', 'course_id', 'student_profile_id');
    }

}
