<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class StoreSessionRequest extends FormRequest
{
    // Menentukan izin akses untuk request ini.
    public function authorize(): bool
    {
        return Auth::user()->role === 'lecturer';
    }

    // Menentukan aturan validasi.
    public function rules(): array
    {
        return [
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) {
                    $query->where('lecturer_id', Auth::id());
                }),
            ],
            'session_date' => ['required', 'date', 'after_or_equal:today'],
            'class_name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'learning_type' => ['required', 'in:online,offline'],
            'location_id' => [
                'required_if:learning_type,offline',
                'nullable',
                Rule::exists('locations', 'id'),
            ],
            'topic' => ['nullable', 'string', 'max:255'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:180'],
        ];
    }

    // Menentukan pesan error khusus.
    public function messages()
    {
        return [
            'course_id.exists' => 'Mata kuliah tidak valid atau bukan mata kuliah yang Anda ampu.',
            'class_name.required' => 'Nama Kelas wajib diisi (contoh: 3KA15).',
            'session_date.after_or_equal' => 'Tanggal sesi tidak boleh di masa lalu.',
            'location_id.required_if' => 'Lokasi wajib dipilih untuk sesi Offline.',
            'late_tolerance_minutes.required' => 'Batas toleransi terlambat wajib diisi.',
        ];
    }

    // Menangani aksi withValidator.
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('start_time');
            $end = $this->input('end_time');

            if (!$start || !$end) {
                return;
            }

            try {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $start);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $end);
            } catch (\Exception $e) {
                return;
            }

            if ($endTime->lte($startTime)) {
                $validator->errors()->add('end_time', 'Waktu selesai harus lebih akhir dari waktu mulai.');
            }
        });
    }
}
