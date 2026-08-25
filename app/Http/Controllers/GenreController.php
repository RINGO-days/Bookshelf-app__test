<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Http\Requests\GenreCreateRequest;

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
    public function store(GenreCreateRequest $request)
    {
        Genre::create($request->validated());
        return redirect('/genres')->with('success',"「{$request->name}」を追加しました。");
    }

    public function edit(Genre $genre)
    {
        return view('genres.edit',compact('genre'));
    }

    public function update(GenreCreateRequest $request,Genre $genre)
    {
        $oldName = $genre->name;
        $genre->update($request->validated());
        return redirect("/genres")->with('success',"「{$oldName}」を「{$genre->name}」に変更しました。");
    }

    public function destroy(Genre $genre)
    {
        if($genre->books()->exists()){
            return back()->with('error',"「{$genre->name}」に紐付いている書籍があるため、削除できません。");
        }
        $genre->delete();
        return back()->with('success', "「{$genre->name}」を削除しました。");;
    }
}
