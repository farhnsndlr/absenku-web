<?php

namespace App\Console\Commands;

use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeAttendanceSessions extends Command
{
    protected $signature = 'attendance:purge {--months=4 : Hapus data lebih lama dari jumlah bulan ini} {--all-finished : Hapus seluruh sesi yang sudah selesai/terlewat}';

    protected $description = 'Menghapus sesi presensi lama beserta data pendukung (foto/bukti).';

    // Menjalankan proses pembersihan sesi presensi lama.
    public function handle(): int
    {
        $deleteAllFinished = (bool) $this->option('all-finished');
        $cutoff = null;

        if (!$deleteAllFinished) {
            $months = (int) $this->option('months');
            if ($months < 1) {
                $this->error('Nilai months harus minimal 1.');
                return self::FAILURE;
            }
            $cutoff = Carbon::now()->subMonths($months)->startOfDay();
            $this->info("Menghapus sesi presensi sebelum {$cutoff->toDateString()}...");
        } else {
            $this->info('Menghapus seluruh sesi presensi yang sudah selesai/terlewat...');
        }

        $totalSessions = 0;
        $totalRecords = 0;

        $sessionQuery = AttendanceSession::query();
        if ($deleteAllFinished) {
            $today = Carbon::today()->toDateString();
            $nowTime = Carbon::now()->format('H:i:s');
            $sessionQuery->where(function ($query) use ($today, $nowTime) {
                $query->where('session_date', '<', $today)
                    ->orWhere(function ($inner) use ($today, $nowTime) {
                        $inner->where('session_date', '=', $today)
                            ->where('end_time', '<', $nowTime);
                    });
            });
        } else {
            $sessionQuery->whereDate('session_date', '<', $cutoff->toDateString());
        }

        $sessionQuery
            ->orderBy('id')
            ->chunkById(50, function ($sessions) use (&$totalSessions, &$totalRecords) {
                foreach ($sessions as $session) {
                    $records = $session->records()
                        ->select('id', 'photo_path', 'supporting_document_path')
                        ->get();

                    foreach ($records as $record) {
                        if ($record->photo_path) {
                            Storage::disk('public')->delete($record->photo_path);
                        }
                        if ($record->supporting_document_path) {
                            Storage::disk('public')->delete($record->supporting_document_path);
                        }
                    }

                    $totalRecords += $records->count();
                    $session->delete();
                    $totalSessions++;
                }
            });

        $this->info("Selesai. Sesi dihapus: {$totalSessions}, record dihapus: {$totalRecords}.");

        return self::SUCCESS;
    }
}
