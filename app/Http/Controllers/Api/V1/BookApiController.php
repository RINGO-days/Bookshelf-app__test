<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;
use App\Http\Resources\BookResource;
use App\Http\Requests\Api\IndexBookRequest;
use App\Http\Requests\Api\StoreBookRequest;

class BookApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexBookRequest $request)
    {
        $query = Book::query();

        $query->when($request->query('published_date'),function($query,$publishedDate){
            return $query->where('published_date',$publishedDate);
        });

        $query->when($request->query('genre'),function($query,$genre){
            return $query->whereHas('genres',function($q) use ($genre){
                $q->where('name',$genre);
            });
        });
        $query->when($request->query('keyword'),function($query,$keyword){
            $query->where('title','like','%'.$keyword.'%');
        });

        $perPage = $request->query('per_page', 10);
        $books = $query->with([
            'genres',
            'reviews',
        ])->withAvg('reviews','rating')
            ->withCount('reviews')
            ->paginate($perPage);

        return BookResource::collection($books)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth()->id();

        $book = Book::create($validated);
        $genresId = Genre::whereIn('name',$validated['genres'])
            ->pluck('id');
        $book->genres()->attach($genresId);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load([
            'reviews',
            'genres'
        ])->loadAvg('reviews','rating')
            ->loadCount('reviews');

        return (new BookResource($book))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
