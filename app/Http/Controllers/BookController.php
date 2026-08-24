<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Http\Requests\BookReviewRequest;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::paginate(10);
        return view('books.index',compact('books'));
    }

    public function show(Book $book)
    {
        return view('books.show',compact('book'));
    }

    public function favorite(Book $book)
    {
        $user = Auth()->user();
        $user->favoriteBooks()->toggle($book->id);

        return back();
    }

    public function review(BookReviewRequest $request,Book $book)
    {
        Review::create([
            'user_id' => Auth()->id(),
            'book_id' => $book->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back();
    }

    public function create()
    {
        $genres = Genre::all();
        return view('books.create',compact('genres'));
    }

    public function edit(Book $book)
    {
        $genres = Genre::all();
        return view('books.edit',compact('book','genres'));
    }
}
