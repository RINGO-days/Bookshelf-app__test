<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\View\View;

class RankingController extends Controller
{
    /**
     * ランキング画面を表示する
     * Bookモデルのリレーションからreviewテーブルの評価数を取得
     * 評価高い順に１０件取得し、それに伴うレビュー数も取得する
     *
     * @return View
     */
    public function ranking(): View
    {
        $rankedBooks = Book::has('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->take(10)
            ->withCount('reviews')
            ->get();

        return view('ranking.index', compact('rankedBooks'));
    }
}
