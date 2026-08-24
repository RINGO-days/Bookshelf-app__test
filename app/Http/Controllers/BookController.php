<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Http\Requests\BookReviewRequest;
use App\Http\Requests\BookCreateRequest;

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
