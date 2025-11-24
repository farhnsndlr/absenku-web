<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    } // Admin boleh akses

    public function rules(): array
    {
        return [
            // Data Akun Utama
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // butuh field password_confirmation di form
            'role' => ['required', 'in:admin,lecturer,student'],

            // Data Profil (Validasi Bersyarat)
            // required_if: field ini wajib JIKA field lain bernilai tertentu
            'nid' => ['nullable', 'required_if:role,lecturer', 'string', 'max:20', 'unique:lecturer_profiles,nid'],
            'npm' => ['nullable', 'required_if:role,student', 'string', 'max:20', 'unique:student_profiles,npm'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ];
    }

    // Opsional: Kustomisasi pesan error
    public function messages(): array
    {
        return [
            'nid.required_if' => 'NID wajib diisi untuk peran Dosen.',
            'npm.required_if' => 'NPM wajib diisi untuk peran Mahasiswa.',
        ];
    }
}
