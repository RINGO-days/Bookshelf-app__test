<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Book;
use App\Models\User;
use Ramsey\Collection\Collection;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = [
            5 => [
                '素晴らしい本でした！',
                '人生が変わりました。',
                '何度も読み返しています。'
            ],
            4 => [
                'とても参考になりました。',
                '読みやすくておすすめです。',
                '期待通りの内容でした。'
            ],
            3 => [
                '普通でした。',
                '可もなく不可も無く。',
                '期待したほどではなかった。'
            ],
            2 => [
                '少し期待はずれでした。',
                '内容が薄い印象。',
                'もう少し深掘りして欲しかった。'
            ],
            1 => [
                '残念ながら合いませんでした。',
                '期待と違いました。'
            ]
        ];

        $books = Book::all();
        $users = User::all();
        $books->map(function ($book) use($users,$template) {
            $reviewCount = rand(2, 4);
            $randomUsers = $users->random($reviewCount);
            $randomUsers->map(function ($user) use ($book,$template) {
                $rating = rand(1,5);
                $comment = collect($template[$rating])->random();
                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => $comment
                ]);
            });
        });
    }
}
