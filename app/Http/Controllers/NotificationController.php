<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * ナビゲーションの通知マークより、通知の一覧画面を表示する
     *
     * @return View
     */
    public function index(): View
    {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 通知一覧画面にて、まだ未読の通知を既読にするアクション
     *
     * @param int $notificationId
     * @return RedirectResponse
     */
    public function read($notificationId): RedirectResponse
    {
        auth()->user()->notifications()->where('id', $notificationId)
            ->update([
                'read_at' => now()
            ]);

        return back();
    }
}
