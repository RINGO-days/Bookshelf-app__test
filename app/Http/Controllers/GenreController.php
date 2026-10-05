<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Http\Requests\GenreCreateRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class GenreController extends Controller
{
    /**
     * ジャンル一覧の画面を表示する
     *
     * @return View
     */
    public function list(): View
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンル一覧画面から選択したジャンルに紐付いている書籍を表示する
     *
     * @param Genre $genre
     * @return View
     */
    public function show(Genre $genre): View
    {
        $books = $genre->books()->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * 新規でジャンルを作成する画面を表示表示する
     *
     * @return View
     */
    public function create(): View
    {
        return view('genres.create');
    }

    /**
     * ジャンル作成画面でジャンルを作成するアクション
     *
     * @param GenreCreateRequest $request
     * @return RedirectResponse
     */
    public function store(GenreCreateRequest $request): RedirectResponse
    {
        DB::transaction(function() use($request){
            Genre::create($request->validated());
        });

        return redirect('/genres')->with('success', "ジャンルを作成しました。");
    }

    /**
     * 登録したジャンルを編集する画面を表示
     *
     * @return View
     */
    public function edit(Genre $genre): View
    {
        return view('genres.edit', compact('genre'));
    }

    /**
     * ジャンル編集画面からジャンルを更新するアクション
     *
     * @param GenreCreateRequest $request
     * @return RedirectResponse
     */
    public function update(GenreCreateRequest $request, Genre $genre): RedirectResponse
    {
        $oldName = $genre->name;

        DB::transaction(function() use($request,$genre){
            $genre->update($request->validated());
        });

        return redirect("/genres")->with('success', "ジャンルを更新しました。");
    }

    /**
     * ジャンル一覧画面から登録されているジャンルを削除するアクション
     *
     * @param Genre $genre
     * @return RedirectResponse
     */
    public function destroy(Genre $genre): RedirectResponse
    {
        if ($genre->books()->exists()) {
            return back()->with('error', "このジャンルに紐付いている書籍があるため、削除できません。");
        }

        DB::transaction(function() use($genre){
            $genre->delete();
        });

        return back()->with('success', "ジャンルを削除しました。");;
    }
}
