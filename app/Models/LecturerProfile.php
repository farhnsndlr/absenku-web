<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'full_name',
        'phone_number',
    ];

    // Relasi ke User (One-to-One)
    public function user()
    {
        return $this->hasOne(User::class, 'profile_id');
    }

    // Relasi ke Mata Kuliah yang diampu (One-to-Many)
    public function courses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }
}
