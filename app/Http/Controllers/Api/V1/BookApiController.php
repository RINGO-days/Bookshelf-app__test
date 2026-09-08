<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;
use App\Http\Resources\BookResource;
use App\Http\Requests\Api\IndexBookRequest;
use App\Http\Requests\Api\StoreBookRequest;
use App\Http\Requests\Api\UpdateBookRequest;
use Illuminate\Http\JsonResponse;

class BookApiController extends Controller
{
    /**
     * 絞り込み可能な書籍一覧のindexアクション
     * クエリパラメータによって、出版日、ジャンル、部分一致のキーワード検索、１ページの表示件数を指定し、情報を取得
     *
     * @param IndexBookRequest $request
     * @return JsonResponse
     */
    public function index(IndexBookRequest $request): JsonResponse
    {
        $query = Book::query();

        $query->when($request->query('published_date'), function ($query, $publishedDate) {
            return $query->where('published_date', $publishedDate);
        });
        $query->when($request->query('genre'), function ($query, $genre) {
            return $query->whereHas('genres', function ($q) use ($genre) {
                $q->where('name', $genre);
            });
        });
        $query->when($request->query('keyword'), function ($query, $keyword) {
            $query->where('title', 'like', '%' . $keyword . '%');
        });

        $perPage = $request->query('per_page', 10);
        $books = $query->with([
            'genres',
            'reviews',
        ])->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate($perPage);

        return BookResource::collection($books)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * 送られてきたbodyデータによって、書籍を登録するstoreアクション
     *
     * @param StoreBookRequest $request
     * @return JsonResponse
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth()->id();

        $book = Book::create($validated);
        $genresId = Genre::whereIn('name', $validated['genres'])
            ->pluck('id');
        $book->genres()->attach($genresId);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * パスパラメータに書籍のIDを指定して、書籍の情報を取得するshowアクション
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function show(Book $book): JsonResponse
    {
        $book->load([
            'reviews',
            'genres'
        ])->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return (new BookResource($book))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * 本人のみ可能のpolicy
     * パスパラメータに書籍IDを入力し、bodyデータに変更するデータを送信し更新を行うupdateアクション
     *
     * @param UpdateBookRequest $request
     * @param Book #book
     * @return JsonResponse
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $this->authorize('update', $book);
        $book->update($request->validated());

        $book->load([
            'reviews',
            'genres'
        ])->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return (new BookResource($book))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * 本人のみ可能のpolicy
     * パスパラメータに書籍IDを入力し、書籍を削除するdestroyアクション
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);
        $book->delete();

        return response()->json(null, 204);
    }
}
