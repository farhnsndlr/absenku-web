<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule

class CourseStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; } // Admin boleh akses

    public function rules(): array
    {
        return [
            // Kode MK harus unik
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
            'course_name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'lecturer_id' => [
                'required',
                // Validasi bahwa ID yang dipilih ada di tabel users DAN rolenya 'lecturer'
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'lecturer');
                }),
            ],
        ];
    }

    // (Opsional) Pesan error kustom
    public function messages(): array
    {
        return [
            'lecturer_id.required' => 'Dosen pengampu wajib dipilih.',
            'lecturer_id.exists' => 'Dosen yang dipilih tidak valid.',
            'course_time.date_format' => 'Format waktu harus HH:MM (contoh: 08:00).',
        ];
    }
}
