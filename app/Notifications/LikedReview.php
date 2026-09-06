<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Review;

class LikedReview extends Notification
{
    use Queueable;
    public $review;
    public $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review, User $liker)
    {
        $this->review = $review;
        $this->liker = $liker;
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
            'review_id' => $this->review->id,
            'book_title' => $this->review->book->title,
            'liker_name' => $this->liker->name,
            'title' => "レビューへのいいね！",
            'body' => "{$this->liker->name}があなたのレビュー({$this->review->book->title})に「いいね！」をしました。",
            'timing' => now()->diffForHumans(),
        ];
    }
}
