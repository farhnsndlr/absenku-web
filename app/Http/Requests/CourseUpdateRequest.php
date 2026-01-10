<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseUpdateRequest extends FormRequest
{
    // Menentukan izin akses untuk request ini.
    public function authorize(): bool { return true; }

    // Menentukan aturan validasi.
    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'course_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'course_code')->ignore($course->id)
            ],
            'course_name' => ['required', 'string', 'max:255'],
            'course_time' => ['required', 'date_format:H:i'],
            'lecturer_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'lecturer');
                }),
            ],
        ];
    }

}
