<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use App\Notifications\LikedReview;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * 自身が投稿したレビューを編集する画面の表示
     *
     * @param Review $review
     * @return View
     */
    public function edit(Review $review): View
    {
        return view('reviews.edit', compact('review'));
    }

    /**
     * レビューに対していいねをするアクション
     * いいね時にレビューをしたユーザーが本人ではない場合、レビューしたユーザーに通知が飛ぶ
     *
     * @param Review #review
     * @return RedirectResponse
     */
    public function like(Review $review): RedirectResponse
    {
        $review->likedByUsers()->toggle(Auth()->id());

        $reviewOwner = $review->user;
        if ($reviewOwner->id !== auth()->id()) {
            $reviewOwner->notify(new LikedReview($review, auth()->user()));
        }

        return back();
    }

    /**
     * 自身が投稿したレビューを削除するアクション
     *
     * @param Review $review
     * @return RedirectResponse
     */
    public function destroy(Review $review): RedirectResponse
    {
        DB::transaction(function()use($review){
            $review->delete();
        });

        return back();
    }

    /**
     * レビュー編集画面にて、レビューを更新するアクション
     *
     * @param ReviewRequest $request
     * @return RedirectResponse
     */
    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        DB::transaction(function()use($request,$review){
            $review->update($request->validated());
        });

        return redirect("/books/{$review->book->id}");
    }
}
