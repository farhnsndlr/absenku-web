<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class AdminAttendancePurged extends Notification
{
    use Queueable;

    private Carbon $timestamp;

    // Menangani aksi __construct.
    public function __construct(?Carbon $timestamp = null)
    {
        $this->timestamp = $timestamp ?? Carbon::now();
    }

    // Menentukan channel notifikasi.
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // Menentukan payload notifikasi.
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pembersihan Presensi',
            'message' => 'Penghapusan data presensi dilakukan pada ' . $this->timestamp->translatedFormat('d M Y H:i'),
            'type' => 'warning',
        ];
    }
}
