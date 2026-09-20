<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Services\ReportService;

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

        $reportService = app(ReportService::class);
        $statsData = $reportService->getStats($user);
        $response->assertViewHas('stats', function ($stats) use ($statsData) {
            return $stats['summary']['total_reviews'] === $statsData['summary']['total_reviews']
            && $stats['summary']['books_read'] === $statsData['summary']['books_read']
            && $stats['summary']['average_rating'] === $statsData['summary']['average_rating']
            && collect($stats['rating_distribution'])->toArray() === collect($statsData['rating_distribution'])->toArray()
            && collect($stats['top_rated_books'])->toArray() === collect($statsData['top_rated_books'])->toArray()
            && collect($stats['genre_ratings'])->toArray() === collect($statsData['genre_ratings'])->toArray();
        });
    }
}
