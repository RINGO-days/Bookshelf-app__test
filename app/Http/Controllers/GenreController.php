<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function list()
    {
        $genres = Genre::withCount('books')->get();
        return view('genres.index',compact('genres'));
    }

    public function show(Genre $genre)
    {
        $books = $genre->books()->paginate(6);
        return view('genres.show',compact('genre','books'));
    }

    public function create()
    {
        return view('genres.create');
    }

    public function edit(Genre $genre)
    {
        return view('genres.edit',compact('genre'));
    }
}
