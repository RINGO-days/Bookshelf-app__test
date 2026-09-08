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
    /**
     * Create a new notification instance.
     */
    public function __construct($plan)
    {
        $this->plan = $plan;
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
        return [
            'plan_id' => $this->plan->id,
            'book_title' => $this->plan->book->title,
            'title' => '読書計画の期日の超過',
            'body' => "{$this->plan->book->title}の読書の期日が過ぎました。"
        ];
    }
}
