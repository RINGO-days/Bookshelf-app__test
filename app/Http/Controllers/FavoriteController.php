<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * 自分がお気に入り登録した書籍の一覧画面の表示
     *
     * @param View
     */
    public function list(): View
    {
        $books = Auth()->user()->favoriteBooks()->paginate(10);

        return view('favorites.index', compact('books'));
    }
}
