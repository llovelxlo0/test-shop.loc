<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    public function markAllRead() 
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
    public function markRead(DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === auth()->id(), 403);

        $notification->markAsRead();
        return back();
    }
}
