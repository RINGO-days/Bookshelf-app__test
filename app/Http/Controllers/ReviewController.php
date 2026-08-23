<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function edit(Review $review)
    {
        return view('reviews.edit',compact('review'));
    }
}
