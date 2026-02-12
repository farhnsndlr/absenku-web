<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    // Menandai semua notifikasi sebagai dibaca.
    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return back();
    }

    // Mengambil notifikasi yang belum dibaca (JSON).
    public function unread(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notifikasi',
                    'message' => $notification->data['message'] ?? 'Notifikasi baru.',
                    'type' => $notification->data['type'] ?? 'info',
                    'time' => $notification->created_at->diffForHumans(),
                ];
            })
            ->values();

        return response()->json($notifications);
    }

    // Menandai satu notifikasi sebagai dibaca.
    public function markRead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['string'],
        ]);

        $request->user()->unreadNotifications()
            ->whereIn('id', $data['ids'])
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }
}
