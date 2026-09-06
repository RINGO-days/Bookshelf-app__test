<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->orderBy('created_at','desc')
            ->get();

        return view('notifications.index',compact('notifications'));
    }

    public function read($notificationId)
    {
        auth()->user()->notifications()->where('id',$notificationId)
            ->update([
                'read_at' => now()
            ]);

        return back();
    }
}
