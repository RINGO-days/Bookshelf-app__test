<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;

class GetTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_書籍一覧を取得するindex(): void
    {
        $user = User::factory()->create();
        Book::create([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);
        Book::create([
            'title' => 'テスト本2',
            'author' => 'テスト著者2',
            'isbn' => '1234567891232',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('api/v1/books');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);
        $response->assertJsonFragment([
            'title' => 'テスト本2',
            'author' => 'テスト著者2',
            'isbn' => '1234567891232',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);
    }
    public function test_指定した書籍情報を取得するshow(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('api/v1/books/'.$book->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);
    }
}
