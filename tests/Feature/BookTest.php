<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;

class BookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_書籍の一覧が表示される(): void
    {
        $user = User::factory()->create();
        Book::create([
            'title' => 'テスト本1',
            'author' => 'テスト著者1',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        Book::create([
            'title' => 'テスト本2',
            'author' => 'テスト著者2',
            'isbn' => '1234567891232',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $response = $this->get('/books');
        $response->assertStatus(200);
        $response->assertSee('テスト本1');
        $response->assertSee('テスト本2');
    }

    public function test_書籍のタイトルを押すと、書籍詳細画面に遷移する(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $response = $this->get('/books/' . $book->id);
        $response->assertStatus(200);
        $response->assertSee('テスト本');
    }

    public function test_書籍の新規登録画面を表示でき、新規登録ができる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bookData = [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => '2020-02-02',
            'user_id' => $user->id,
            'genres' => [$genre->id]
        ];

        $response = $this->actingAs($user)->get('/books/create');
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post('/books/store', $bookData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('books', [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => '2020-02-02',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('book_genre', [
            'genre_id' => $genre->id
        ]);
    }

    public function test_書籍の新規登録時、タイトルが未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bookData = [
            'title' => '',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => '2020-02-02',
            'user_id' => $user->id,
            'genres' => [$genre->id]
        ];
        $response = $this->actingAs($user)->post('/books/store', $bookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'title' => 'タイトルは必須です。'
        ]);
    }
    public function test_書籍の新規登録時、著者が未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bookData = [
            'title' => 'テスト本',
            'author' => '',
            'isbn' => '1234567891234',
            'published_date' => '2020-02-02',
            'user_id' => $user->id,
            'genres' => [$genre->id]
        ];
        $response = $this->actingAs($user)->post('/books/store', $bookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'author' => '著者は必須です。'
        ]);
    }
    public function test_書籍の新規登録時、ISBNが未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bookData = [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '',
            'published_date' => '2020-02-02',
            'user_id' => $user->id,
            'genres' => [$genre->id]
        ];
        $response = $this->actingAs($user)->post('/books/store', $bookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'isbn' => 'ISBNは必須です。'
        ]);
    }
    public function test_書籍の新規登録時、出版日が未入力の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bookData = [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => '',
            'user_id' => $user->id,
            'genres' => [$genre->id]
        ];
        $response = $this->actingAs($user)->post('/books/store', $bookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'published_date' => '出版日は必須です。'
        ]);
    }
    public function test_書籍の新規登録時、ジャンルが未選択の場合バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'テストジャンル'
        ]);
        $bookData = [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => '2020-02-02',
            'user_id' => $user->id,
            'genres' => []
        ];
        $response = $this->actingAs($user)->post('/books/store', $bookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'genres' => 'ジャンルは1つ以上を選択してください。'
        ]);
    }
    public function test_書籍の編集画面に遷移し、書籍の編集ができる(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user)->get('/books/' . $book->id . '/edit');
        $response->assertStatus(200);

        $newGenre = Genre::create([
            'name' => '変更後テストジャンル'
        ]);
        $newBookData = [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->format('Y-m-d'),
            'user_id' => $user->id,
            'genres' => [$newGenre->id]
        ];
        $response = $this->actingAs($user)->put('/books/' . $book->id, $newBookData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('books', [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->format('Y-m-d'),
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $newGenre->id
        ]);
    }
    public function test_書籍の編集時、タイトルが未入力だとバリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $newGenre = Genre::create([
            'name' => '変更後テストジャンル'
        ]);
        $newBookData = [
            'title' => '',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->format('Y-m-d'),
            'user_id' => $user->id,
            'genres' => [$newGenre->id]
        ];
        $response = $this->actingAs($user)->put('/books/' . $book->id, $newBookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'title' => 'タイトルは必須です。'
        ]);
    }
    public function test_書籍の編集時、著者が未入力だとバリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $newGenre = Genre::create([
            'name' => '変更後テストジャンル'
        ]);
        $newBookData = [
            'title' => '変更後テスト本',
            'author' => '',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->format('Y-m-d'),
            'user_id' => $user->id,
            'genres' => [$newGenre->id]
        ];
        $response = $this->actingAs($user)->put('/books/' . $book->id, $newBookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'author' => '著者は必須です。'
        ]);
    }
    public function test_書籍の編集時、ISBNが未入力だとバリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $newGenre = Genre::create([
            'name' => '変更後テストジャンル'
        ]);
        $newBookData = [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '',
            'published_date' => now()->subDays(1)->format('Y-m-d'),
            'user_id' => $user->id,
            'genres' => [$newGenre->id]
        ];
        $response = $this->actingAs($user)->put('/books/' . $book->id, $newBookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'isbn' => 'ISBNは必須です。'
        ]);
    }
    public function test_書籍の編集時、出版日が未入力だとバリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $newGenre = Genre::create([
            'name' => '変更後テストジャンル'
        ]);
        $newBookData = [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => '',
            'user_id' => $user->id,
            'genres' => [$newGenre->id]
        ];
        $response = $this->actingAs($user)->put('/books/' . $book->id, $newBookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'published_date' => '出版日は必須です。'
        ]);
    }
    public function test_書籍の編集時、ジャンルが未選択だとバリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $newGenre = Genre::create([
            'name' => '変更後テストジャンル'
        ]);
        $newBookData = [
            'title' => '変更後テスト本',
            'author' => '変更後テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now()->subDays(1)->format('Y-m-d'),
            'user_id' => $user->id,
            'genres' => []
        ];
        $response = $this->actingAs($user)->put('/books/' . $book->id, $newBookData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'genres' => 'ジャンルは1つ以上を選択してください。'
        ]);
    }
    public function test_書籍の削除ができる(): void
    {
        $user = User::factory()->create();
        Genre::create([
            'name' => 'テストジャンル'
        ]);
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete('/books/' . $book->id);
        $response->assertStatus(302);
        $this->assertDatabaseMissing('books',[
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
    }
}
