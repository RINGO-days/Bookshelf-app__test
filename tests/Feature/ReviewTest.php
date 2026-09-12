<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Review;
use App\Models\Book;
use App\Models\Genre;

class ReviewTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_書籍詳細画面にて、レビューの投稿ができる(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $reviewData = [
            'rating' => 5,
            'comment' => 'テストレビュー'
        ];
        $response = $this->actingAs($user)->post('/books/' . $book->id . '/review', $reviewData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reviews', [
            'rating' => 5,
            'comment' => 'テストレビュー'
        ]);
    }
    public function test_レビュー投稿時、評価数が未選択の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $reviewData = [
            'rating' => '',
            'comment' => 'テストレビュー'
        ];
        $response = $this->actingAs($user)->post('/books/' . $book->id . '/review', $reviewData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'rating' => '評価数を選択してください。'
        ]);
    }
    public function test_レビュー投稿時、コメントが未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $reviewData = [
            'rating' => 5,
            'comment' => ''
        ];
        $response = $this->actingAs($user)->post('/books/' . $book->id . '/review', $reviewData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'comment' => 'コメントは必須です。'
        ]);
    }
    public function test_レビューの編集画面に遷移し、レビューの編集ができる(): void
    {
        $user = User::factory()->create();
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
        $response = $this->actingAs($user)->get('/reviews/' . $review->id . '/edit');
        $response->assertStatus(200);

        $newReviewData = [
            'rating' => 1,
            'comment' => '変更後テストレビュー'
        ];
        $response = $this->actingAs($user)->put('/reviews/' . $review->id, $newReviewData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reviews', [
            'rating' => 1,
            'comment' => '変更後テストレビュー'
        ]);
    }
    public function test_レビューの編集時、評価数が未選択の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
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
        $newReviewData = [
            'rating' => '',
            'comment' => '変更後テストレビュー'
        ];
        $response = $this->actingAs($user)->put('/reviews/' . $review->id, $newReviewData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'rating' => '評価数を選択してください。'
        ]);
    }
    public function test_レビューの編集時、コメントが未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
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
        $newReviewData = [
            'rating' => 1,
            'comment' => ''
        ];
        $response = $this->actingAs($user)->put('/reviews/' . $review->id, $newReviewData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'comment' => 'コメントは必須です。'
        ]);
    }
    public function test_レビューの削除ができる(): void
    {
        $user = User::factory()->create();
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
        $response = $this->actingAs($user)->delete('/reviews/' . $review->id);
        $response->assertStatus(302);
        $this->assertDatabaseMissing('reviews', [
            'rating' => 5,
            'comment' => 'テストレビュー'
        ]);
    }
}
