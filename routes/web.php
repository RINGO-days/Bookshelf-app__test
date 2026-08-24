<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RankingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::middleware('auth')->group(function(){
    Route::prefix('/books')->group(function(){
        Route::get('/create',[BookController::class,'create'])->name('books.create');
        Route::post('/store',[BookController::class,'store'])->name('books.store');
        Route::get('/{book}',[BookController::class,'show'])->name('books.show');
        Route::get('/{book}/edit',[BookController::class,'edit'])->name('books.edit');
        Route::post('/{book}/update',[BookController::class,'update'])->name('books.update');
    });

    Route::prefix('/genre')->group(function(){
        Route::get('/',[GenreController::class,'list'])->name('genres.index');
        Route::get('/show/{genre}',[GenreController::class,'show'])->name('genres.show');
        Route::get('/create',[GenreController::class,'create'])->name('genres.create');
        Route::post('/store',[GenreController::class,'store'])->name('genres.store');
        Route::get('/{genre}/edit',[GenreController::class,'edit'])->name('genres.edit');
        Route::post('/{genre}/update',[GenreController::class,'update'])->name('genres.update');
        Route::post('/destroy',[GenreController::class,'destroy'])->name('genres.destroy');
    });

    Route::prefix('/review')->group(function(){
        Route::get('{review}/edit',[ReviewController::class,'edit'])->name('reviews.edit');
        Route::post('/store',[ReviewController::class,'store'])->name('reviews.store');
        Route::post('/update',[ReviewController::class,'update'])->name('reviews.update');
        Route::post('/like',[ReviewController::class,'like'])->name('reviews.like');
    });
    Route::prefix('/favorite')->group(function(){
        Route::get('/', [FavoriteController::class, 'list'])->name('favorites.index');
        Route::post('/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    });

    Route::get('/ranking',[RankingController::class,'ranking'])->name('ranking.index');
});
