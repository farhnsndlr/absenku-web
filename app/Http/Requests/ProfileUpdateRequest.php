<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\LecturerProfile;
use App\Models\StudentProfile;

class ProfileUpdateRequest extends FormRequest
{
    // Menentukan izin akses untuk request ini.
    public function authorize(): bool
    {
        return true;
    }

    // Menentukan aturan validasi.
    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];

        if ($user->profile_type === LecturerProfile::class) {
            $rules['nid'] = ['required', 'string', 'max:20'];
        } elseif ($user->profile_type === StudentProfile::class) {
            $rules['npm'] = ['required', 'string', 'max:20'];
            $rules['class_name'] = ['required', 'string', 'max:50'];
        }

        return $rules;
    }
}
