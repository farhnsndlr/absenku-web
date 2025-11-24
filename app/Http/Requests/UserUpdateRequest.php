<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil user yang sedang diedit dari route binding
        $user = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            // Ignore ID user ini saat cek unique email
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Nullable saat edit
            'phone_number' => ['nullable', 'string', 'max:20'],
        ];

        // Validasi NID/NPM hanya jika rolenya sesuai.
        // Kita asumsikan Admin TIDAK MENGUBAH ROLE saat edit (untuk menyederhanakan).
        if ($user->role === 'lecturer') {
            $rules['nid'] = [
                'required',
                'string',
                'max:20',
                // Ignore ID profile dosen ini saat cek unique NID
                Rule::unique(LecturerProfile::class)->ignore($user->profile_id)
            ];
        } elseif ($user->role === 'student') {
            $rules['npm'] = [
                'required',
                'string',
                'max:20',
                // Ignore ID profile mahasiswa ini saat cek unique NPM
                Rule::unique(StudentProfile::class)->ignore($user->profile_id)
            ];
        }

        return $rules;
    }
}
