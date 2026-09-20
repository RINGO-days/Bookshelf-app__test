<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ReadingPlan;
use App\Models\Book;
use Illuminate\Support\Carbon;

class ReadingPlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('name','山田太郎')->first();
        $books = Book::take(6)->get();

        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $books[0]->id,
            'target_date' => Carbon::today()->addDays(3),
            'status' => 'in_progress'
        ]);

        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $books[1]->id,
            'target_date' => Carbon::today(),
            'status' => 'in_progress'
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $books[2]->id,
            'target_date' => Carbon::today()->subDays(3),
            'status' => 'in_progress'
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $books[3]->id,
            'target_date' => Carbon::today()->subDays(7),
            'status' => 'in_progress'
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $books[4]->id,
            'target_date' => Carbon::today()->addDays(10),
            'completed_at' => Carbon::today()->subDays(5),
            'status' => 'completed'
        ]);

        $otherUser = User::where('name','鈴木花子')->first();
        ReadingPlan::create([
            'user_id' => $otherUser->id,
            'book_id' => $books[5]->id,
            'target_date' => Carbon::today()->addDays(10),
            'status' => 'in_progress'
        ]);
    }
}
