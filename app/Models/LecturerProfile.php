<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nid',
        'full_name',
        'phone_number',
    ];

    // Relasi ke user.
    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    // Relasi ke mata kuliah yang diajar.
    public function courses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }
}
