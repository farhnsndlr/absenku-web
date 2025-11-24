<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validasi password saat ini (harus cocok dengan di database)
            'current_password' => ['required', 'current_password'],
            // Validasi password baru (harus dikonfirmasi dan memenuhi standar keamanan)
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
