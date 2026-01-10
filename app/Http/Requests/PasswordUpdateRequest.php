<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateRequest extends FormRequest
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
        $usesPassword = !empty($user?->password);

        return [
            'current_password' => $usesPassword ? ['required', 'current_password'] : ['nullable'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
