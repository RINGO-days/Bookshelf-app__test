<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function list(): View
    {
        $books = Auth()->user()->favoriteBooks()->paginate(10);

        return view('favorites.index', compact('books'));
    }
}
