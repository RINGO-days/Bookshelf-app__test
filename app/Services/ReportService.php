<?php

namespace App\Services;

use App\Models\User;
use App\Enums\ReadingPlanStatus;

class ReportService
{
    /**
     * ユーザーの読書の統計データを取得する
     * 二次元配列を使用し、中に統計データを入れていく
     * **[summary] ユーザーのレビューした総回数、読書計画で読了になっている計画の数、レビューの平均の評価数を取得
     *
     * **[rating_distribution] 評価数として０から４までのキーの数字を用意する（Viewファイルで＋１される）
     * mapWithKeyにて'評価数' => 'レビューのカウント' の配列を作成
     *
     * **[top_rated_books] ユーザーがレビューした書籍の高評価順に５件取得し、mapで書籍情報の配列を作成
     *
     * **[genre_rating] ユーザーがレビューした書籍とそのジャンルを取得する
     * flatMapにて一度すべてのデータをただの配列に変換し、mapにてジャンルごとのID、名前、評価数の連想配列を作成
     * その後groupBy(genreId)にてジャンルごとにさきほどの連想配列をまとめる
     * mapにてジャンルID、ジャンル名、ジャンル配列の件数、評価数の平均の配列に整形
     * 最後に平均評価数の高い順に並べて、キー名がジャンルIDになっているためvalueで連番に振り直す
     *
     * @param User $user
     * @return array
     */
    public function getStats(User $user) : array
    {
        return [
            'summary' => [
                'total_reviews' => $user->reviews()->count(),
                'books_read' => $user->plans()->where('status', ReadingPlanStatus::Completed)->count(),
                'average_rating' => $user->reviews()->avg('rating'),
            ],
            'rating_distribution' => collect(range(0, 4))->mapWithKeys(function ($rating) use ($user) {
                $count = $user->reviews()->where('rating', $rating + 1)->count();
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
    }
}