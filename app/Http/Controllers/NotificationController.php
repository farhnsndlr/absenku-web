<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Inject Request di parameter method
    public function markAllAsRead(Request $request)
    {
        // Ambil user dari objek request
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
