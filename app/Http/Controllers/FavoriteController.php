<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function list()
    {
        $books = Auth()->user()->favoriteBooks()->paginate(10);
        return view('favorites.index',compact('books'));
    }
}
