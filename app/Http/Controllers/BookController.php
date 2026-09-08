<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Http\Requests\BookReviewRequest;
use App\Http\Requests\BookCreateRequest;
use App\Notifications\FavoriteBook;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * 書籍一覧がページネーション（１０）で表示される画面の表示
     * クエリパラメータによって、部分一致キーワード、ジャンルを絞り込み可能
     * また、登録日、評価数、５０音で並び替え可能
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Book::query();
        $query->when($request->query('keyword'), function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('author', 'like', '%' . $keyword . '%');
            });
        });
        $query->when($request->query('genre'), function ($query, $genre) {
            $query->whereHas('genres', function ($q) use ($genre) {
                $q->where('genres.id', $genre);
            });
        });

        $query->withAvg('reviews', 'rating')->withCount('reviews');

        if ($request->query('sort') === 'newest') {
            $query->orderBy('updated_at', 'desc');
        } elseif ($request->query('sort') === 'oldest') {
            $query->orderBy('updated_at', 'asc');
        } elseif ($request->query('sort') === 'rating') {
            $query->orderBy('reviews_avg_rating', 'desc');
        } elseif (($request->query('sort') === 'title')) {
            $query->orderBy('title', 'asc');
        }

        $books = $query->paginate(10);

        $genres = Genre::all();
        return view('books.index', compact('books', 'genres'));
    }

    /**
     * 書籍の詳細画面の表示
     *
     * @param Book $book
     * @return View
     */
    public function show(Book $book): View
    {
        return view('books.show', compact('book'));
    }

    /**
     * 書籍詳細画面にてお気に入りボタンを押すアクション
     * 中間テーブル（favoriteBooks）にてトグル操作
     * お気に入りをした書籍の登録者に、通知が入る
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function favorite(Book $book): RedirectResponse
    {
        $user = Auth()->user();
        $user->favoriteBooks()->toggle($book->id);

        if ($book->user_id !== auth()->id()) {
            $book->user->notify(new FavoriteBook($book, auth()->user()));
        }

        return back();
    }

    /**
     * 書籍詳細画面にて、評価並びにレビューを付けるアクション
     *
     * @param BookReviewRequest $request
     * @param Book $book
     * @return RedirectResponse
     */
    public function review(BookReviewRequest $request, Book $book): RedirectResponse
    {
        Review::create(array_merge($request->validated(), [
            'user_id' => Auth()->id(),
            'book_id' => $book->id,
        ]));

        return back();
    }

    /**
     * 書籍詳細画面にて、自分が登録した書籍を削除するアクション
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect('/books');
    }

    /**
     * 新しく書籍を登録する画面の表示
     *
     * @return View
     */
    public function create(): View
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 新規の書籍登録画面にて外部API(Google book)でISBNを入力することで書籍情報を自動で入力するアクション
     *
     * @param string $isbn
     * @return JsonResponse
     */
    public function isbnSearch($isbn): JsonResponse
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => 'isbn:' . $isbn,
            'key' => env('GOOGLE_BOOKS_API_KEY')
        ]);
        $volumeInfo = $response->json('items.0.volumeInfo');

        return response()->json([
            'title' => $volumeInfo['title'] ?? '',
            'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : '',
            'description' => $volumeInfo['description'] ?? '',
            'image_url' => $volumeInfo['imageLinks']['thumbnail'] ?? '',
            'published_date' => $volumeInfo['publishedDate'] ?? '',
        ]);
    }

    /**
     * 新規の書籍登録画面にて書籍登録を行うアクション
     *
     * @param BookCreateRequest $request
     * @return RedirectResponse
     */
    public function store(BookCreateRequest $request): RedirectResponse
    {
        Book::create(array_merge($request->validated(), [
            'user_id' => Auth()->id()
        ]));

        return redirect('/books');
    }

    /**
     * 登録した書籍情報を編集する画面の表示
     *
     * @param Book $book
     * @return View
     */
    public function edit(Book $book): View
    {
        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍編集画面にて書籍情報を更新するアクション
     *
     * @param BookCreateRequest $request
     * @param Book $book
     * @return RedirectResponse
     */
    public function update(BookCreateRequest $request, Book $book): RedirectResponse
    {
        $book->update($request->validated());
        $book->genres()->attach($request->genres);

        return redirect("/books/$book->id");
    }
}
