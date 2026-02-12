<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use App\Models\StudentProfile;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $filters;

    // Menangani aksi __construct.
    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // Menyusun data untuk export.
    public function collection()
    {
        $query = AttendanceRecord::query();


        if (!empty($this->filters['session_id'])) {
            $query->where('session_id', $this->filters['session_id']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereHas('session', function ($q) {
                $q->where('session_date', '>=', $this->filters['start_date']);
            });
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereHas('session', function ($q) {
                $q->where('session_date', '<=', $this->filters['end_date']);
            });
        }

        if (!empty($this->filters['course_id'])) {
            $query->whereHas('session', function ($q) {
                $q->where('course_id', $this->filters['course_id']);
            });
        }

        if (!empty($this->filters['class_name'])) {
            $query->whereHas('session', function ($q) {
                $q->where('class_name', $this->filters['class_name']);
            });
        }

        if (!empty($this->filters['session_type'])) {
            $query->whereHas('session', function ($q) {
                $q->where('learning_type', $this->filters['session_type']);
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['lecturer_id'])) {
            $query->whereHas('session.course', function ($q) {
                $q->where('lecturer_id', $this->filters['lecturer_id']);
            });
        }

        $query->with([
            'session.course',
            'session.location',
            'student.user',
        ]);

        $query->orderBy(
            \App\Models\AttendanceSession::select('session_date')
                ->whereColumn('attendance_sessions.id', 'attendance_records.session_id'),
            'desc'
        )->orderBy(
            StudentProfile::select('full_name')
                ->whereColumn('student_profiles.id', 'attendance_records.student_id'),
            'asc'
        );

        return $query->get();
    }

    // Menentukan judul kolom export.
    public function headings(): array
    {
        return [
            'NPM Mahasiswa',
            'Nama Mahasiswa',
            'Mata Kuliah',
            'Kode MK',
            'Nama Kelas',
            'Tanggal Sesi',
            'Jam Sesi',
            'Tipe Sesi',
            'Lokasi Kampus',
            'Status Kehadiran',
            'Waktu Submit Absen',
            'Bukti Foto',
            'Mode Absen (Lokasi)',
            'Koordinat Absen',
        ];
    }

    // Memetakan data untuk baris export.
    public function map($record): array
    {

        $sessionDate = $record->session->session_date instanceof Carbon ? $record->session->session_date->format('d-m-Y') : $record->session->session_date;
        $startTime = $record->session->start_time instanceof Carbon ? $record->session->start_time->format('H:i') : $record->session->start_time;
        $endTime = $record->session->end_time instanceof Carbon ? $record->session->end_time->format('H:i') : $record->session->end_time;
        $submissionTime = $record->submission_time instanceof Carbon ? $record->submission_time->format('H:i:s') : ($record->submission_time ?? '-');

        $studentProfile = $record->student;
        $studentUser = $studentProfile?->user;
        $npm = $studentProfile?->npm ?? '-';
        $studentName = $studentProfile?->full_name ?? $studentUser?->name ?? '-';

        $sessionType = $record->session->learning_type ?? $record->session->session_type;

        return [
            $npm,
            $studentName,
            $record->session->course->course_name ?? '-',
            $record->session->course->course_code ?? '-',
            $record->session->class_name ?? '-',
            $sessionDate,
            $startTime . ' - ' . $endTime,
            $sessionType ? ucfirst($sessionType) : '-',
            $record->session->location->location_name ?? ($sessionType == 'online' ? 'Online (Daring)' : '-'),
            $this->formatStatus($record->status),
            $submissionTime,
            $record->photo_path ? route('attendance.media', $record->photo_path) : '-',
            ucfirst($record->learning_type),
            $record->location_maps ?? '-',
        ];
    }

    // Menentukan styling untuk export.
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
            ],
        ];
    }

    // Menangani aksi title.
    public function title(): string
    {
        return 'Laporan Kehadiran';
    }

    private function formatStatus($status)
    {
        $statusLabels = [
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'sick' => 'Sakit/Izin'
        ];

        return $statusLabels[$status] ?? ucfirst($status);
    }
}
