<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;

class UserUpdateRequest extends FormRequest
{
    // Menentukan izin akses untuk request ini.
    public function authorize(): bool
    {
        return true;
    }

    // Menentukan aturan validasi.
    public function rules(): array
    {
        $user = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ];

        if ($user->role === 'lecturer') {
            $rules['nid'] = [
                'required',
                'string',
                'max:20',
                Rule::unique(LecturerProfile::class)->ignore($user->profile_id)
            ];
        } elseif ($user->role === 'student') {
            $rules['npm'] = [
                'required',
                'string',
                'max:20',
                Rule::unique(StudentProfile::class)->ignore($user->profile_id)
            ];
            $rules['class_name'] = ['required', 'string', 'max:50'];
        }

        return $rules;
    }
}
