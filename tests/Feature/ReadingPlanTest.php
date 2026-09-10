<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\ReadingPlan;
use App\Models\Book;

class ReadingPlanTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_読書計画の一覧が表示できる(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->get('/reading-plans');
        $response->assertStatus(200);
        $response->assertSee($book->title);
        $response->assertSee(now()->addMonths(1)->format('Y-m-d'));
    }
    public function test_読書計画の一覧のステータスの絞り込みができる(): void
    {
        $user = User::factory()->create();
        $wantBook = Book::create([
            'title' => '読みたいテスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $completedBook = Book::create([
            'title' => '読んだテスト本',
            'author' => 'テスト著者',
            'isbn' => '1111111111111',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $wantBook->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $completedBook->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
            'status' => 'completed'
        ]);

        $response = $this->actingAs($user)->get('/reading-plans?status=want');
        $response->assertStatus(200);
        $response->assertSee('読みたいテスト本');
        $response->assertDontSee('読んだ本');

        $response = $this->actingAs($user)->get('/reading-plans?status=completed');
        $response->assertSee('読んだテスト本');
        $response->assertDontSee('読みたい本');
    }
    public function test_読書計画のステータスを「読了」にできる(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $readingPlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->post('/reading-plans/' . $readingPlan->id . '/complete');
        $response->assertStatus(302);
        $this->assertDatabaseHas('reading_plans', [
            'status' => 'completed'
        ]);
    }
    public function test_読書計画の作成画面へ遷移し、読書計画を作成することができる(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/reading-plans/create');
        $response->assertStatus(200);

        $readingPlanData = [
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d')
        ];
        $response = $this->actingAs($user)->post('/reading-plans/store', $readingPlanData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reading_plans', [
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d')
        ]);
    }
    public function test_読書計画を作成時、バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);

        $readingPlanData = [
            'book_id' => '',
            'target_date' => ''
        ];
        $response = $this->actingAs($user)->post('/reading-plans/store', $readingPlanData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'book_id' => '書籍を選択してください。',
            'target_date' => '期日は必須です。'
        ]);
    }
    public function test_読書計画を更新することができる(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $readingPlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
        ]);

        $newReadingPlanData = [
            'target_date' => now()->addMonths(2)->format('Y-m-d')
        ];
        $response = $this->actingAs($user)->put('/reading-plans/' . $readingPlan->id . '/update', $newReadingPlanData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reading_plans', [
            'target_date' => now()->addMonths(2)->format('Y-m-d')
        ]);
    }
    public function test_読書計画を更新時、バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $readingPlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
        ]);

        $newReadingPlanData = [
            'target_date' => ''
        ];
        $response = $this->actingAs($user)->put('/reading-plans/' . $readingPlan->id . '/update', $newReadingPlanData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'target_date' => '期日は必須です。'
        ]);
    }
    public function test_読書計画を削除することができる(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        $readingPlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->delete('/reading-plans/' . $readingPlan->id . '/destroy');
        $response->assertStatus(302);
        $this->assertDatabaseMissing('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addMonths(1)->format('Y-m-d'),
        ]);
    }
    public function test_読書計画の期日の3日前の場合、ユーザーに通知が届く(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->subDays(3)->format('Y-m-d'),
        ]);
        $this->artisan('app:check-3daysAgo');
        $notification = $user->notifications()->first();
        $this->assertEquals("{$book->title}の読書の期日が3日後となりました。", $notification->data['body']);
    }
    public function test_読書計画の期日が過ぎた場合、ステータスが「期日過ぎ」に変更されユーザーに通知が届く(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'isbn' => '1234567891234',
            'published_date' => now(),
            'user_id' => $user->id,
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->subMonths(1)->format('Y-m-d'),
        ]);
        $this->artisan('app:check-expired');
        $this->assertDatabaseHas('reading_plans', [
            'status' => 'expired'
        ]);
        $notification = $user->notifications()->first();
        $this->assertEquals('テスト本の読書の期日が過ぎました。', $notification->data['body']);
    }
}
