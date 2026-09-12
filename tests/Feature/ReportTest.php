<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\Review;

class ReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_ユーザーのレポート画面が表示され、サマリー情報が表示される(): void
    {
        $this->seed();
        $user = User::first();
        $book = Book::first();
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->subMonths(1)->format('Y-m-d'),
            'status' => 'completed'
        ]);

        $response = $this->actingAs($user)->get('/report');
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) use ($user) {
            $reviewCount = $user->reviews()->count();
            return $stats['summary']['total_reviews'] === $reviewCount;
        });
        $response->assertViewHas('stats', function ($stats) use ($user) {
            $completedCount = $user->plans()->where('status', 'completed')->count();
            return $stats['summary']['books_read'] === $completedCount;
        });
        $response->assertViewHas('stats', function ($stats) use ($user) {
            $avgRating = $user->reviews()->avg('rating');
            return $stats['summary']['average_rating'] === $avgRating;
        });
    }
}
