<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;

class LikeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_レビューの投稿に対して「いいね」ができ、また解除ができる(): void
    {
        $user = User::factory()->create();
        $likedByUser = User::factory()->create();

        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $review = Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストレビュー'
        ]);
        $response = $this->actingAs($likedByUser)->post('/reviews/' . $review->id . '/like');
        $response->assertStatus(302);
        $this->assertDatabaseHas('reviewLike', [
            'review_id' => $review->id,
            'user_id' => $likedByUser->id
        ]);
        $response = $this->actingAs($likedByUser)->post('/reviews/' . $review->id . '/like');
        $response->assertStatus(302);
        $this->assertDatabaseMissing('reviewLike', [
            'review_id' => $review->id,
            'user_id' => $likedByUser->id
        ]);
    }
}
