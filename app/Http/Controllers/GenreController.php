<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Http\Requests\GenreCreateRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GenreController extends Controller
{
    public function list(): View
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    public function show(Genre $genre): View
    {
        $books = $genre->books()->paginate(6);
        return view('genres.show', compact('genre', 'books'));
    }

    public function create(): View
    {
        return view('genres.create');
    }
    public function store(GenreCreateRequest $request): RedirectResponse
    {
        Genre::create($request->validated());
        return redirect('/genres')->with('success', "「{$request->name}」を追加しました。");
    }

    public function edit(Genre $genre): View
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(GenreCreateRequest $request, Genre $genre): RedirectResponse
    {
        $oldName = $genre->name;
        $genre->update($request->validated());
        return redirect("/genres")->with('success', "「{$oldName}」を「{$genre->name}」に変更しました。");
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        if ($genre->books()->exists()) {
            return back()->with('error', "「{$genre->name}」に紐付いている書籍があるため、削除できません。");
        }
        $genre->delete();
        return back()->with('success', "「{$genre->name}」を削除しました。");;
    }
}
