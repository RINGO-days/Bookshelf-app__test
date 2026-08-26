<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Book;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => Carbon::parse($this->published_date)->format('Y-m-d'),
            'description' => $this->description,
            'image_url' => $this->image_url,
            'user_id' => $this->user_id,
            'genre' => $this->genres->pluck('name'),
            'rating' => round($this->reviews_avg_rating,2),
            'number_of_reviews' => $this->reviews_count,
        ];
    }
}
