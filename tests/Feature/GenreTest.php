<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Genre;
use App\Models\Book;
use App\Models\User;

class GenreTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_ジャンルの一覧を表示でき、それに紐付いている書籍の件数も表示できる(): void
    {
        $user = User::factory()->create();
        $genre1 = Genre::create([
            'name' => 'テスト1'
        ]);
        $book1 = Book::create([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $book1->genres()->sync([$genre1->id]);

        $book2 = Book::create([
            'title' => 'テスト本2',
            'author' => 'テスト著者2',
            'isbn' => '1234567891232',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $book2->genres()->sync([$genre1->id]);

        $genre2 = Genre::create([
            'name' => 'テスト2'
        ]);

        $response = $this->actingAs($user)->get('/genres');
        $response->assertStatus(200);

        $response->assertSee('2冊');
        $response->assertSee($genre1->name);
        $response->assertSee($genre2->name);
    }
    public function test_ジャンル名をクリック後、そのジャンルに紐付いている書籍の一覧が表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テスト2'
        ]);
        $book1 = Book::create([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $book1->genres()->sync([$genre->id]);

        $book2 = Book::create([
            'title' => 'テスト本2',
            'author' => 'テスト著者2',
            'isbn' => '1234567891232',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $book2->genres()->sync([$genre->id]);

        $response = $this->actingAs($user)->get('/genres/show/'.$genre->id);
        $response->assertStatus(200);
        $response->assertSee($book1->name);
        $response->assertSee($book2->name);
    }
    public function test_ジャンル作成画面へ遷移でき、新規でジャンルを登録することができる(): void
    {
        $user = User::factory()->create();
        $genreData = [
            'name' => 'テストジャンル'
        ];
        $response = $this->actingAs($user)->get('/genres/create');
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post('/genres', $genreData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('genres', [
            'name' => 'テストジャンル'
        ]);
    }
    public function test_ジャンルを登録時、ジャンル名が未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $genreData = [
            'name' => ''
        ];

        $response = $this->actingAs($user)->post('/genres', $genreData);
        $response->assertSessionHasErrors([
            'name' => 'ジャンル名は必須です。'
        ]);
    }
    public function test_ジャンル編集画面へ遷移し、ジャンル名を編集することができる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テスト'
        ]);
        $newGenre = [
            'name' => '変更後テストジャンル'
        ];
        $response = $this->actingAs($user)->get('/genres/' . $genre->id . '/edit');
        $response->assertStatus(200);

        $response = $this->actingAs($user)->put('/genres/' . $genre->id, $newGenre);
        $response->assertStatus(302);
        $this->assertDatabaseHas('genres', [
            'name' => '変更後テストジャンル'
        ]);
    }
    public function test_ジャンル名を編集時、ジャンル名が未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テスト'
        ]);
        $newGenre = [
            'name' => ''
        ];

        $response = $this->actingAs($user)->put('/genres/' . $genre->id, $newGenre);
        $response->assertSessionHasErrors([
            'name' => 'ジャンル名は必須です。'
        ]);
    }
    public function test_ジャンルを削除することができる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);

        $response = $this->actingAs($user)->delete('/genres/' . $genre->id);
        $response->assertStatus(302);
        $this->assertDatabaseMissing('genres', [
            'name' => $genre->name
        ]);
    }
}
