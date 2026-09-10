<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;

class ReviewLikeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $reviews = Review::all();
        foreach($reviews as $review){
            $allUserId = User::pluck('id')->toArray();

            $selfUserId = $review->user_id;

            $targetUserId = array_diff($allUserId, [$selfUserId]);
            shuffle($targetUserId);
            $likeCount = rand(1, 3);
            $randomUserId = collect($targetUserId)->random($likeCount)->all();

            $review->likedByUsers()->syncWithoutDetaching($randomUserId);
        }
    }
}
