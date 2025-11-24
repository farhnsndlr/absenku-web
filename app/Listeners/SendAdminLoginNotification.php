<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
// Import notifikasi dan facade
use App\Notifications\AdminLoginAlert;
use Illuminate\Support\Facades\Notification;

class SendAdminLoginNotification
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // $event->user adalah user yang baru saja login
        $user = $event->user;

        // Cek apakah user memiliki role 'admin'
        if ($user->role === 'admin') {
            // Kirim notifikasi HANYA ke user admin yang sedang login itu sendiri
            $user->notify(new AdminLoginAlert());
        }
    }
}
