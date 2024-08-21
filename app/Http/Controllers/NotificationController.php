<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch the notifications
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        // Optionally mark them as read when the user visits the notifications page
        $user->unreadNotifications->markAsRead();

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
