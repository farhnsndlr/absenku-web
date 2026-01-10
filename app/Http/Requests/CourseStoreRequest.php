<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseStoreRequest extends FormRequest
{
    // Menentukan izin akses untuk request ini.
    public function authorize(): bool { return true; }

    // Menentukan aturan validasi.
    public function rules(): array
    {
        return [
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
            'course_name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'lecturer_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'lecturer');
                }),
            ],
        ];
    }

    // Menentukan pesan error khusus.
    public function messages(): array
    {
        return [
            'lecturer_id.required' => 'Dosen pengampu wajib dipilih.',
            'lecturer_id.exists' => 'Dosen yang dipilih tidak valid.',
            'course_time.date_format' => 'Format waktu harus HH:MM (contoh: 08:00).',
        ];
    }
}
