<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;

class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_書籍詳細画面にて、お気に入り登録ができる。(): void
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
        $response = $this->actingAs($likedByUser)->post('/books/' . $book->id . '/favorite');
        $response->assertStatus(302);
        $this->assertDatabaseHas('favoriteBooks', [
            'book_id' => $book->id
        ]);
    }
    public function test_ユーザーが登録したお気に入り書籍の一覧が表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $user->favoriteBooks()->sync($book->id);

        $response = $this->actingAs($user)->get('/favorite');
        $response->assertStatus(200);
        $response->assertSee($book->title);
    }
}
