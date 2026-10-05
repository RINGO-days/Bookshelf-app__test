<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanReminderNotification extends Notification
{
    use Queueable;

    public $plan;
    public string $timing;
    /**
     * Create a new notification instance.
     */
    public function __construct($plan,$timing)
    {
        $this->plan = $plan;
        $this->timing = $timing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $bookTitle = $this->plan->book->title;

        $title = $title = match ($this->timing) {
            '3days-ago'   => '読書計画の期日まであと3日',
            'today'       => '読書計画の期日（本日）',
            '3days-later' => '読書計画の期限超過',
        };
        $message = match($this->timing){
            '3days-ago' => "「{$bookTitle}」の読書の期日が3日前となりました。",
            'today' => "「{$bookTitle}」の読書の期日の当日となりました。",
            '3days-later' => "「{$bookTitle}」の読書の期日から3日が過ぎました。"
        };
        return [
            'plan_id' => $this->plan->id,
            'book_title' => $this->plan->book->title,
            'title' => $title,
            'body' => $message,
        ];
    }
}
