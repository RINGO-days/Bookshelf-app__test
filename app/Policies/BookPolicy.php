<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Book $book)
    {
        return $user->id === $book->user_id;
    }
    public function delete(User $user, Book $book)
    {
        return $user->id === $book->user_id;
    }
}
