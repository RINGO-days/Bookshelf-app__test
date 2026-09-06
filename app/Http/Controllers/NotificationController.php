<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function read($notificationId): RedirectResponse
    {
        auth()->user()->notifications()->where('id', $notificationId)
            ->update([
                'read_at' => now()
            ]);

        return back();
    }
}
