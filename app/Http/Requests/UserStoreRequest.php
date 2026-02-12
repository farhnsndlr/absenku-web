<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    // Menentukan izin akses untuk request ini.
    public function authorize(): bool
    {
        return true;
    }

    // Menentukan aturan validasi.
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,lecturer,student'],

            'nid' => ['nullable', 'required_if:role,lecturer', 'string', 'max:20', 'unique:lecturer_profiles,nid'],
            'npm' => ['nullable', 'required_if:role,student', 'string', 'max:20', 'unique:student_profiles,npm'],
            'class_name' => ['nullable', 'required_if:role,student', 'string', 'max:50'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ];
    }

    // Menentukan pesan error khusus.
    public function messages(): array
    {
        return [
            'nid.required_if' => 'NID wajib diisi untuk peran Dosen.',
            'npm.required_if' => 'NPM wajib diisi untuk peran Mahasiswa.',
        ];
    }
}
