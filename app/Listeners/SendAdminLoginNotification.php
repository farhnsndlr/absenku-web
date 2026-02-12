<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\AdminLoginAlert;
use Illuminate\Support\Facades\Notification;

class SendAdminLoginNotification
{
    // Menangani event atau job.
    public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user->role === 'admin') {
            $user->notify(new AdminLoginAlert());
        }
    }
}
