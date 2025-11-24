<?php

namespace App\Exports;

use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = AttendanceSession::with(['course', 'location']);

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->where('session_date', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->where('session_date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['course_id'])) {
            $query->where('course_id', $this->filters['course_id']);
        }

        if (!empty($this->filters['session_type'])) {
            $query->where('session_type', $this->filters['session_type']);
        }

        $sessions = $query->latest('session_date')->get();

        $data = collect();

        foreach ($sessions as $session) {
            $records = AttendanceRecord::with(['student.user'])
                ->where('session_id', $session->id)
                ->get();

            foreach ($records as $record) {
                $data->push([
                    'session' => $session,
                    'record' => $record
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tanggal Sesi',
            'Mata Kuliah',
            'Kode MK',
            'Waktu Mulai',
            'Waktu Selesai',
            'Tipe Sesi',
            'Lokasi',
            'NPM',
            'Nama Mahasiswa',
            'Waktu Absen',
            'Status',
            'Mode Pembelajaran',
            'Koordinat GPS'
        ];
    }

    public function map($row): array
    {
        $session = $row['session'];
        $record = $row['record'];

        // $record->student sudah berupa objek StudentProfile
        $studentProfile = $record->student;

        return [
            $session->session_date->format('Y-m-d') ?? '-', // Asumsi session_date di-cast
            $session->course->course_name ?? '-',
            $session->course->course_code ?? '-',
            $session->start_time,
            $session->end_time,
            ucfirst($session->session_type),
            $session->location->location_name ?? 'Online',
            $studentProfile->npm ?? '-',
            $studentProfile->full_name ?? $studentProfile->user->name ?? '-', // Ambil dari full_name, fallback ke user->name
            $record->submission_time ? $record->submission_time->format('H:i:s') : '-', // Asumsi submission_time di-cast
            $this->formatStatus($record->status),
            ucfirst($record->learning_type),
            $record->location_maps ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID, // Menggunakan konstanta dari PhpOffice\PhpSpreadsheet\Style\Fill
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }

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

        return $statusLabels[$status] ?? $status;
    }
}
