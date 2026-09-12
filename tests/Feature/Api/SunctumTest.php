<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Genre;
use App\Models\Book;
use Laravel\Sanctum\Sanctum;

class SunctumTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_未認証時にpost系APIで401が返る()
    {
        $user = User::factory()->create();
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bodyData = [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'genres' => [$genre->id]
        ];

        $response = $this->postJson('/api/v1/books', $bodyData);
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);

        $book = Book::create([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $response = $this->putJson('/api/v1/books/' . $book->id, $bodyData);
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);

        $response = $this->deleteJson('/api/v1/books/' . $book->id);
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }
    public function test_他ユーザーの書籍を更新・削除しようとすると403が返る()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $otherUser = User::factory()->create([
            'id' => 999
        ]);

        $otherBook = Book::create([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $otherUser->id,
        ]);

        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bodyData = [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->toDateString(),
            'genres' => [$genre->id]
        ];

        $response = $this->actingAs($user)->putJson('/api/v1/books/' . $otherBook->id, $bodyData);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'この書籍を管理する権限がありません。'
        ]);
        $response = $this->actingAs($user)->deleteJson('/api/v1/books/' . $otherBook->id);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'この書籍を管理する権限がありません。'
        ]);
    }
}
