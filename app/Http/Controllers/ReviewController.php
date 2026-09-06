<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use App\Notifications\LikedReview;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function edit(Review $review): View
    {
        return view('reviews.edit', compact('review'));
    }

    public function like(Review $review): RedirectResponse
    {
        $review->likedByUsers()->toggle(Auth()->id());

        $reviewOwner = $review->user;

        if ($reviewOwner->id !== auth()->id()) {
            $reviewOwner->notify(new LikedReview($review, auth()->user()));
        }

        return back();
    }
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back();
    }

    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        $review->update($request->validated());

        return redirect("/books/{$review->book->id}");
    }
}
