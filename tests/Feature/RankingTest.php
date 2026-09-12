<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;

class RankingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_ランキング画面が表示され、top10が表示される(): void
    {
        $this->seed();
        $response = $this->get('ranking');
        $response->assertStatus(200);

        $response->assertViewHas('rankedBooks',function ($books){
            return count($books) === 10;
        });
    }
}
