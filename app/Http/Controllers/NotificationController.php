<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::orderBy('id', 'desc')->take(15)->get();
        $unreadCount = AppNotification::unread()->count();

        return response()->json([
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead()
    {
        AppNotification::unread()->update(['is_read' => true]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Semua notifikasi telah ditandai dibaca.',
        ]);
    }
}
