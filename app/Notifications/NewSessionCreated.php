<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\AttendanceSession;

class NewSessionCreated extends Notification
{
    use Queueable;

    public $session;

    // Menangani aksi __construct.
    public function __construct(AttendanceSession $session)
    {
        $this->session = $session;
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
            'session_id' => $this->session->id,
            'course_name' => $this->session->course->course_name,
            'title' => 'Sesi Presensi Baru',
            'message' => 'Sesi presensi baru untuk ' . $this->session->course->course_name,
            'url' => route('student.attendance.index'),
            'type' => 'info',
        ];
    }
}
