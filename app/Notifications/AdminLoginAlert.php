<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// Import Request untuk mendapatkan IP dan User Agent
use Illuminate\Support\Facades\Request;

class AdminLoginAlert extends Notification
{
    use Queueable;

    // Kita bisa menambahkan ShouldQueue jika ingin dikirim di latar belakang
    // class AdminLoginAlert extends Notification implements ShouldQueue

    protected $ipAddress;
    protected $userAgent;
    protected $loginTime;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // Ambil informasi konteks login saat notifikasi dibuat
        $this->ipAddress = Request::ip();
        $this->userAgent = Request::userAgent();
        $this->loginTime = now()->translatedFormat('d F Y, H:i T');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Kita simpan di database. Bisa tambahkan 'mail' juga jika mau.
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => '⚠️ Peringatan Keamanan Login',
            'message' => "Akun Admin Anda baru saja digunakan untuk login dari IP: {$this->ipAddress} pada {$this->loginTime}.",
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'type' => 'security_alert', // Tipe untuk membedakan ikon/warna
            'url' => route('profile.password'), // Link untuk segera ganti password
        ];
    }
}
