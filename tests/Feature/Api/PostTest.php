<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\Hash;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_ログインを行い、トークンを得ることができる(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
        $bodyData = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];
        $response = $this->actingAs($user)->postJson('/api/v1/login', $bodyData);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);
    }
    public function test_書籍を作成するstore(): void
    {
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
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/books', $bodyData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('books', [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('book_genre', [
            'genre_id' => $genre->id
        ]);
    }
    public function test_バリデーションエラー時に422と日本語エラーメッセージが返る()
    {
        $user = User::factory()->create();
        $bodyData = [
            'title' => '',
            'author' => '',
            'isbn' => '',
            'published_date' => '',
            'genres' => []
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/books', $bodyData);
        $response->assertStatus(422);
        $response->assertjson([
            'errors' => [
                'title' => ['タイトルは必須です。'],
                'author' => ['著者は必須です。'],
                'isbn' => ['ISBNは必須です。'],
                'published_date' => ['出版日は必須です。'],
                'genres' => ['ジャンルは1つ以上を入力してください。']
            ]
        ]);
    }
    public function test_書籍を更新するupdate(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $bodyData = [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->toDateString(),
            'genres' => [$genre->name]
        ];
        $response = $this->actingAs($user, 'sanctum')->putJson('/api/v1/books/' . $book->id, $bodyData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('books', [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->toDateString(),
        ]);
        $this->assertDatabaseHas('book_genre', [
            'genre_id' => $genre->id
        ]);
    }
    public function test_書籍を削除するdelete(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/books/' . $book->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('books', [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->toDateString(),
        ]);
        $this->assertDatabaseMissing('book_genre', [
            'genre_id' => $genre->id
        ]);
    }
}
