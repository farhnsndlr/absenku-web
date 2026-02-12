<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Request;

class AdminLoginAlert extends Notification
{
    use Queueable;


    protected $ipAddress;
    protected $userAgent;
    protected $loginTime;

    // Menangani aksi __construct.
    public function __construct()
    {
        $this->ipAddress = Request::ip();
        $this->userAgent = Request::userAgent();
        $this->loginTime = now()->translatedFormat('d F Y, H:i T');
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
            'title' => '⚠️ Peringatan Keamanan Login',
            'message' => "Akun Admin Anda baru saja digunakan untuk login dari IP: {$this->ipAddress} pada {$this->loginTime}.",
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'type' => 'security_alert',
            'url' => route('profile.password'),
        ];
    }
}
