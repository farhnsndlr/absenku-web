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
        'profile_photo_path', // Pastikan ini tetap ada
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
                // Cek apakah ada path foto disimpan di database
                if (isset($attributes['profile_photo_path']) && $attributes['profile_photo_path']) {
                    // Jika ada, generate URL publik lengkap menggunakan Storage facade
                    // Hasilnya misal: http://domain.com/storage/profile-photos/namafile.jpg
                    return Storage::url($attributes['profile_photo_path']);
                }

                // Jika tidak ada foto, kembalikan null.
                // Nanti di view kita akan cek: if($user->profile_photo_url) { tampilkan img } else { tampilkan inisial }
                return null;
            },
        );
    }

    // Hapus relasi hasOne/belongsTo yang lama untuk lecturerProfile dan studentProfile
    // Ganti dengan relasi polimorfik 'profile'
    public function profile(): MorphTo
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

    public function students()
    {
        // Parameter ke-2 WAJIB diisi dengan nama tabel pivot kita: 'course_enrollments'
        return $this->belongsToMany(StudentProfile::class, 'course_enrollments', 'course_id', 'student_profile_id');
    }

}
