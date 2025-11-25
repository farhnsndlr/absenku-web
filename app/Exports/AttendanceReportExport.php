<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Agar kolom otomatis menyesuaikan lebar
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Mengambil data yang sudah difilter dan di-eager load.
     * Jauh lebih efisien dari kode sebelumnya.
     */
    public function collection()
    {
        // Mulai query dari AttendanceRecord, bukan AttendanceSession
        $query = AttendanceRecord::query();

        // --- Terapkan Filter ---

        // Filter berdasarkan ID Sesi spesifik
        if (!empty($this->filters['session_id'])) {
            $query->where('session_id', $this->filters['session_id']);
        }

        // Filter berdasarkan tanggal sesi
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

        // Filter berdasarkan mata kuliah
        if (!empty($this->filters['course_id'])) {
            $query->whereHas('session', function ($q) {
                $q->where('course_id', $this->filters['course_id']);
            });
        }

        // Filter berdasarkan nama kelas
        if (!empty($this->filters['class_name'])) {
            $query->whereHas('session', function ($q) {
                $q->where('class_name', $this->filters['class_name']);
            });
        }

        // Filter berdasarkan tipe sesi (online/offline)
        // PERBAIKAN: Gunakan 'session_type' sesuai konsistensi
        if (!empty($this->filters['session_type'])) {
            $query->whereHas('session', function ($q) {
                $q->where('session_type', $this->filters['session_type']);
            });
        }

        // Filter berdasarkan status kehadiran
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filter agar hanya menampilkan data dari mata kuliah dosen yang login
        // PENTING: Ini harus selalu ada untuk keamanan
        if (!empty($this->filters['lecturer_id'])) {
            $query->whereHas('session.course', function ($q) {
                $q->where('lecturer_id', $this->filters['lecturer_id']);
            });
        }

        // --- Eager Loading (Mengatasi N+1 Query) ---
        // PENTING: Muat semua relasi yang dibutuhkan di sini.
        $query->with([
            'session.course',   // Untuk data MK
            'session.location', // Untuk data lokasi
            'user'
        ]);

        // --- Sorting ---
        // Urutkan berdasarkan tanggal sesi (desc) dan nama mahasiswa (asc)
        $query->orderBy(
            \App\Models\AttendanceSession::select('session_date')
                ->whereColumn('attendance_sessions.id', 'attendance_records.session_id'),
            'desc'
        )->orderBy(
            \App\Models\User::select('name')
                ->whereColumn('users.id', 'attendance_records.student_id'),
            'asc'
        );

        // Eksekusi query dan kembalikan koleksi data
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Sesi',
            'Jam Sesi', // Digabung jadi satu kolom
            'Mata Kuliah',
            'Kode MK',
            'Nama Kelas', // Kolom Baru
            'Tipe Sesi',
            'Lokasi Kampus',
            'NPM Mahasiswa',
            'Nama Mahasiswa',
            'Status Kehadiran',
            'Waktu Submit Absen',
            'Mode Absen (Lokasi)',
        ];
    }

    /**
     * Memetakan setiap record menjadi baris di Excel.
     * $record adalah satu objek AttendanceRecord beserta relasinya yang sudah dimuat.
     */
    public function map($record): array
    {
        // $record adalah instance AttendanceRecord
        // $record->session adalah instance AttendanceSession
        // $record->student adalah instance User (Mahasiswa)

        // Pastikan casting tanggal di model Anda sudah benar
        $sessionDate = $record->session->session_date instanceof Carbon ? $record->session->session_date->format('d-m-Y') : $record->session->session_date;
        $startTime = $record->session->start_time instanceof Carbon ? $record->session->start_time->format('H:i') : $record->session->start_time;
        $endTime = $record->session->end_time instanceof Carbon ? $record->session->end_time->format('H:i') : $record->session->end_time;
        $submissionTime = $record->submission_time instanceof Carbon ? $record->submission_time->format('H:i:s') : ($record->submission_time ?? '-');

        // Asumsi: NPM ada di tabel users atau kita pakai ID sebagai fallback jika belum ada kolom NPM
        $npm = $record->user?->npm ?? $record->user?->id ?? '-';

        return [
            $sessionDate,
            $startTime . ' - ' . $endTime, // Gabung jam mulai dan selesai
            $record->session->course->course_name ?? '-',
            $record->session->course->course_code ?? '-',
            $record->session->class_name ?? '-', // Nama Kelas
            ucfirst($record->session->session_type), // Tipe Sesi (Online/Offline)
            $record->session->location->location_name ?? ($record->session->session_type == 'online' ? 'Online (Daring)' : '-'),
            $npm,
            $record->user?->name ?? '-', // Nama dari tabel users
            $this->formatStatus($record->status),
            $submissionTime,
            ucfirst($record->learning_type), // Mode Absen (Onsite/Remote)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header (baris 1)
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'] // Warna biru header
                ],
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

        return $statusLabels[$status] ?? ucfirst($status);
    }
}
