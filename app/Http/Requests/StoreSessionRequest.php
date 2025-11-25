<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class StoreSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya user dengan role lecturer yang boleh akses
        return Auth::user()->role === 'lecturer';
    }

    public function rules(): array
    {
        return [
            // Course ID wajib ada di tabel courses DAN dosen pengampunya harus user yang sedang login
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) {
                    $query->where('lecturer_id', Auth::id());
                }),
            ],
            // Tanggal sesi wajib date format, dan minimal hari ini (tidak boleh masa lalu)
            'session_date' => ['required', 'date', 'after_or_equal:today'],
            // Nama Kelas (contoh 3KA15)
            'class_name' => ['required', 'string', 'max:50'],
            // Format jam H:i (contoh: 08:00)
            'start_time' => ['required', 'date_format:H:i'],
            // Jam selesai harus setelah jam mulai
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            // Tipe sesi online/offline
            'session_type' => ['required', 'in:online,offline'],
            // Lokasi wajib diisi JIKA tipe sesi offline, boleh null jika online
            'location_id' => [
                'required_if:session_type,offline',
                'nullable',
                Rule::exists('locations', 'id'),
            ],
            // Topik opsional
            'topic' => ['nullable', 'string', 'max:255'],
        ];
    }

    // Pesan error kustom agar lebih jelas
    public function messages()
    {
        return [
            'course_id.exists' => 'Mata kuliah tidak valid atau bukan mata kuliah yang Anda ampu.',
            'class_name.required' => 'Nama Kelas wajib diisi (contoh: 3KA15).',
            'session_date.after_or_equal' => 'Tanggal sesi tidak boleh di masa lalu.',
            'end_time.after' => 'Waktu selesai harus lebih akhir dari waktu mulai.',
            'location_id.required_if' => 'Lokasi wajib dipilih untuk sesi Offline.',
        ];
    }
}
