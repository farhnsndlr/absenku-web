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

    public $session; // Variabel untuk menyimpan data sesi

    /**
     * Create a new notification instance.
     */
    public function __construct(AttendanceSession $session)
    {
        $this->session = $session;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Kita simpan di database saja
    }

    /**
     * Get the array representation of the notification.
     * Data ini yang akan disimpan di kolom 'data' tabel notifications.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'session_id' => $this->session->id,
            'course_name' => $this->session->course->course_name,
            'message' => 'Sesi baru dibuka untuk ' . $this->session->course->course_name,
            'url' => route('student.attendance.create', $this->session->id), // Link langsung ke absen
            'type' => 'info', // info, success, warning, error
        ];
    }
}
