<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class FavoritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $favorites = [
            1 => [1,6,7],
            2 => [3,7,10,11],
            3 => [1,4,8,9],
            4 => [2,5,7],
            5 => [1,7,10,11]
        ];
        foreach($favorites as $userId => $bookId){
            $user = User::find($userId);
            $user->favoriteBooks()->syncWithoutDetaching($bookId);
        }
    }
}
