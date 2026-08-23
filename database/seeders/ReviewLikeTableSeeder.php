<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewLikeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($reviewId = 1; $reviewId <=32; $reviewId++){
            $review = Review::find($reviewId);
            $allUserId = [1,2,3,4,5];

            $selfUserId = $review->user_id;

            $targetUserId = array_diff($allUserId,[$selfUserId]);
            shuffle($targetUserId);
            $likeCount = rand(0,3);
            $randomUserId = collect($targetUserId)->random($likeCount)->all();

            $review->likedByUsers()->syncWithoutDetaching($randomUserId);
        }
    }
}
