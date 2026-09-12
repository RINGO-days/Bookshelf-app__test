<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;

class FavoritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            $favoriteBooks = rand(3, 5);

            $randomBookIds = $books->random($favoriteBooks)->pluck('id');

            $user->favoriteBooks()->syncWithoutDetaching($randomBookIds);
        }
    }
}
