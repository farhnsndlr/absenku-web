<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Izinkan semua user yang login
    }

    public function rules(): array
    {
        $user = $this->user();

        // Aturan dasar untuk tabel users
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // Email harus unik, tapi abaikan untuk user yang sedang login saat ini
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];

        // Aturan tambahan berdasarkan tipe profil
        if ($user->profile_type === LecturerProfile::class) {
            // Validasi NID untuk dosen
            $rules['nid'] = ['required', 'string', 'max:20'];
        } elseif ($user->profile_type === StudentProfile::class) {
            // Validasi NPM untuk mahasiswa
            $rules['npm'] = ['required', 'string', 'max:20'];
        }

        return $rules;
    }
}
