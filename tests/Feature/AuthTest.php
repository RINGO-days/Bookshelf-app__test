<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

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
}
