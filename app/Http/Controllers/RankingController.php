<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class RankingController extends Controller
{
    public function ranking()
    {
        $rankedBooks = Book::has('reviews')
            ->withAvg('reviews','rating')
            ->orderBy('reviews_avg_rating','desc')
            ->take(10)
            ->withCount('reviews')
            ->get();
        return view('ranking.index',compact('rankedBooks'));
    }
}
