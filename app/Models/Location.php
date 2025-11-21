<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_name',
        'latitude',
        'longitude',
        'radius_meters',
    ];

    // Relasi ke Sesi yang menggunakan lokasi ini (One-to-Many)
    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class, 'location_id');
    }
}
