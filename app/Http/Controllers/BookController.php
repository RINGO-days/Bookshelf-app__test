<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;

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
