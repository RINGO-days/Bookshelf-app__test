<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\ReadingPlanStatus;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $stats = [
            'summary' => [
                'total_reviews' => $user->reviews()->count(),
                'books_read' => $user->plans()->where('status', ReadingPlanStatus::Completed)->count(),
                'average_rating' => $user->reviews()->avg('rating'),
            ],
            'rating_distribution' => collect(range(0, 4))->mapWithKeys(function ($rating) use ($user) {
                $count = $user->reviews()->where('rating', $rating)->count();
                return [$rating => $count];
            }),
            'top_rated_books' => $user->reviews()
                ->with('book')
                ->orderBy('rating', 'desc')
                ->take(5)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->book->id,
                        'title' => $review->book->title,
                        'author' => $review->book->author,
                        'rating' => $review->rating,
                    ];
                }),
            'genre_ratings' => $user->reviews()
                ->with('book.genres')
                ->get()
                ->flatMap(function ($review) {
                    return $review->book->genres->map(function ($genre) use ($review) {
                        return [
                            'genre_id' => $genre->id,
                            'name' => $genre->name,
                            'rating' => $review->rating,
                        ];
                    });
                })
                ->groupBy('genre_id')
                ->map(function ($group, $genreId) {
                    return [
                        'id' => $genreId,
                        'name' => $group->first()['name'],
                        'count' => $group->count(),
                        'average_rating' => $group->avg('rating'),
                    ];
                })
                ->sortByDesc('average_rating')
                ->values(),
        ];

        return view('reports.index', compact('stats'));
    }
}
