<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect()->back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}
