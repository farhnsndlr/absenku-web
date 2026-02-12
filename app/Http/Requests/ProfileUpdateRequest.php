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
            $rules['npm'] = [
                'required',
                'string',
                'max:20',
                Rule::unique('student_profiles', 'npm')->ignore($user->profile_id),
            ];
            $rules['class_name'] = ['required', 'string', 'max:50'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'npm.unique' => 'NPM sudah terdaftar. Harap kontak Admin untuk melapor.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('class_name')) {
            $this->merge([
                'class_name' => strtoupper((string) $this->input('class_name')),
            ]);
        }
    }
}
