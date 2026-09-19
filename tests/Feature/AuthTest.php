<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use App\Models\Genre;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_ログイン時メールアドレスが未入力の場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/login');
        $formData = [
            'email' => '',
            'password' => 'password'
        ];
        $response = $this->post('/login', $formData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスは必須です。'
        ]);
    }

    public function test_ログイン時パスワードが未入力の場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/admin/login');
        $formData = [
            'email' => 'user3@example.com',
            'password' => ''
        ];
        $response = $this->post('/login', $formData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは必須です。'
        ]);
    }

    public function test_ログイン時登録内容と一致しない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/admin/login');
        $formData = [
            'email' => 'wrong_user',
            'password' => 'password'
        ];
        $response = $this->post('/login', $formData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません。'
        ]);
    }

    public function test_会員登録時、名前が未入力の場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $formData = [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'testtest',
            'password_confirmation' => 'testtest',
        ];

        $response = $this->post('/register', $formData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'お名前は必須です。',
        ]);
    }

    public function test_会員登録時、メールアドレスが未入力の場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $formData = [
            'name' => 'test_user',
            'email' => '',
            'password' => 'testtest',
            'password_confirmation' => 'testtest'
        ];
        $response = $this->post('/register', $formData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスは必須です。'
        ]);
    }

    public function test_会員登録時、パスワードが8文字未満の場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $formData = [
            'name' => 'test_user',
            'email' => 'test@example.com',
            'password' => 'testtes',
            'password_confirmation' => 'testtest'
        ];
        $response = $this->post('/register', $formData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください。'
        ]);
    }

    public function test_会員登録時、パスワードが一致しない場合、バリデーションメッセージが表示される()
    {
        $response = $this->get('/register');
        $formData = [
            'name' => 'test_user',
            'email' => 'test@example.com',
            'password' => 'testtest',
            'password_confirmation' => 'testexample'
        ];
        $response = $this->post('/register', $formData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません。'
        ]);
    }

    public function test_ログイン時、正常にログアウトでき、ログイン画面へ遷移する()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_未認証ユーザーが書籍お気に入り登録した際、ログイン画面へリダイレクトされる。()
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $response = $this->post('books/' . $book->id . '/favorite');
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーがレビューを投稿した際、ログイン画面へリダイレクトされる。()
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $response = $this->post('books/' . $book->id . '/review');
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーがレビューにいいねした際、ログイン画面へリダイレクトされる。()
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

        $response = $this->post('reviews/' . $review->id . '/like');
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーが書籍お気に入り一覧画面にアクセスした際、ログイン画面へリダイレクトされる。()
    {
        $response = $this->get('/favorite');
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーがジャンル一覧画面にアクセスした際、ログイン画面へリダイレクトされる。()
    {
        $response = $this->get('/genres');
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーがジャンル詳細画面にアクセスした際、ログイン画面へリダイレクトされる。()
    {
        $genre = Genre::create([
            'name' => 'テスト'
        ]);
        $response = $this->get('/genres/show/'.$genre->id);
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーがジャンル登録画面にアクセスした際、ログイン画面へリダイレクトされる。()
    {
        $response = $this->get('/genres/create/');
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーがジャンル編集画面にアクセスした際、ログイン画面へリダイレクトされる。()
    {
        $genre = Genre::create([
            'name' => 'テスト'
        ]);
        $response = $this->get('/genres/'.$genre->id.'/edit');
        $response->assertRedirect('/login');
    }
    public function test_未認証ユーザーが読書計画画面にアクセスした際、ログイン画面へリダイレクトされる。()
    {
        $response = $this->get('/reading-plans');
        $response->assertRedirect('/login');
    }
}
