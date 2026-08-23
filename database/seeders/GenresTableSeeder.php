<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genre::firstOrCreate([
            'id' => 1,
            'name' => '小説'
        ]);
        Genre::firstOrCreate([
            'id' => 2,
            'name' => 'ビジネス'
        ]);
        Genre::firstOrCreate([
            'id' => 3,
            'name' => '技術書'
        ]);
        Genre::firstOrCreate([
            'id' => 4,
            'name' => '自己啓発'
        ]);
        Genre::firstOrCreate([
            'id' => 5,
            'name' => 'エッセイ'
        ]);
        Genre::firstOrCreate([
            'id' => 6,
            'name' => '歴史'
        ]);
        Genre::firstOrCreate([
            'id' => 7,
            'name' => '科学'
        ]);
        Genre::firstOrCreate([
            'id' => 8,
            'name' => '芸術'
        ]);
        Genre::firstOrCreate([
            'id' => 9,
            'name' => '料理'
        ]);
        Genre::firstOrCreate([
            'id' => 10,
            'name' => '旅行'
        ]);
    }
}
