<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Book;
use App\Models\User;

class FavoriteBook extends Notification
{
    use Queueable;

    public $book;
    public $liker;
    /**
     * Create a new notification instance.
     */
    public function __construct(Book $book,User $liker)
    {
        $this->book = $book;
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
            'book_title' => $this->book->title,
            'liker_name' => $this->liker->name,
            'title' => '書籍への「お気に入り」',
            'body' => "あなたが登録した書籍（{$this->book->title}）が、{$this->liker->name}に「お気に入り」されました。",
            'timing' => now()->diffForHumans(),
        ];
    }
}
