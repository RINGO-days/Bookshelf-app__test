<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Http\Requests\BookReviewRequest;
use App\Http\Requests\BookCreateRequest;
use App\Notifications\FavoriteBook;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        $query->when($request->query('keyword'),function($query,$keyword){
            $query->where(function($q) use ($keyword){
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('author', 'like', '%' . $keyword . '%');
            });
        });
        $query->when($request->query('genre'),function ($query, $genre) {
            $query->whereHas('genres',function($q) use ($genre){
                $q->where('genres.id',$genre);
            });
        });

        $query->withAvg('reviews', 'rating')->withCount('reviews');

        if($request->query('sort') === 'newest'){
            $query->orderBy('updated_at','desc');
        }elseif ($request->query('sort') === 'oldest'){
            $query->orderBy('updated_at', 'asc');
        }elseif($request->query('sort') === 'rating'){
            $query->orderBy('reviews_avg_rating','desc');
        }elseif(($request->query('sort') === 'title')){
            $query->orderBy('title', 'asc');
        }

        $books = $query->paginate(10);

        $genres = Genre::all();
        return view('books.index',compact('books','genres'));
    }

    public function show(Book $book)
    {
        return view('books.show',compact('book'));
    }

    public function favorite(Book $book)
    {
        $user = Auth()->user();
        $user->favoriteBooks()->toggle($book->id);

        if($book->user_id !== auth()->id()){
            $book->user->notify(new FavoriteBook($book,auth()->user()));
        }

        return back();
    }

    public function review(BookReviewRequest $request,Book $book)
    {
        Review::create(array_merge($request->validated(),[
            'user_id' => Auth()->id(),
            'book_id' => $book->id,
        ]));

        return back();
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect('/books');
    }

    public function create()
    {
        $genres = Genre::all();
        return view('books.create',compact('genres'));
    }
    public function isbnSearch($isbn)
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes',[
            'q' => 'isbn:'.$isbn,
            'key' => env('GOOGLE_BOOKS_API_KEY')
        ]);
        $volumeInfo = $response->json('items.0.volumeInfo');

        return response()->json([
            'title' => $volumeInfo['title'] ?? '',
            'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : '',
            'description' => $volumeInfo['description'] ?? '',
            'image_url' => $volumeInfo['imageLinks']['thumbnail'] ?? '',
            'published_date' => $volumeInfo['publishedDate'] ?? '',
        ]);
    }

    public function store(BookCreateRequest $request)
    {
        Book::create(array_merge($request->validated(),[
            'user_id' => Auth()->id()
        ]));
        return redirect('/books');
    }

    public function edit(Book $book)
    {
        $genres = Genre::all();
        return view('books.edit',compact('book','genres'));
    }

    public function update(BookCreateRequest $request,Book $book)
    {
        $book->update($request->validated());
        $book->genres()->attach($request->genres);
        return redirect("/books/$book->id");
    }
}
