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


Route::middleware('auth')->group(function(){
    Route::prefix('/books')->group(function(){
        Route::post('/{book}/favorite',[BookController::class,'favorite'])->name('favorites.toggle');
        Route::post('/{book}/review',[BookController::class,'review'])->name('reviews.store');
        Route::get('/create',[BookController::class,'create'])->name('books.create');
        Route::post('/store',[BookController::class,'store'])->name('books.store');
        Route::get('/{book}/edit',[BookController::class,'edit'])->name('books.edit');
        Route::put('/{book}',[BookController::class,'update'])->name('books.update');
        Route::delete('/{book}',[BookController::class,'destroy'])->name('books.destroy');
    });

    Route::prefix('/genres')->group(function(){
        Route::get('/',[GenreController::class,'list'])->name('genres.index');
        Route::post('/',[GenreController::class,'store'])->name('genres.store');
        Route::get('/create',[GenreController::class,'create'])->name('genres.create');
        Route::get('/show/{genre}',[GenreController::class,'show'])->name('genres.show');
        Route::get('/{genre}/edit',[GenreController::class,'edit'])->name('genres.edit');
        Route::put('/{genre}',[GenreController::class,'update'])->name('genres.update');
        Route::delete('/{genre}',[GenreController::class,'destroy'])->name('genres.destroy');
    });

    Route::prefix('/reviews')->group(function(){
        Route::get('{review}/edit',[ReviewController::class,'edit'])->name('reviews.edit');
        Route::delete('{review}',[ReviewController::class,'destroy'])->name('reviews.destroy');
        Route::put('/{review}',[ReviewController::class,'update'])->name('reviews.update');
        Route::post('/{review}/like',[ReviewController::class,'like'])->name('reviews.like');
    });
    Route::prefix('/favorite')->group(function(){
        Route::get('/', [FavoriteController::class, 'list'])->name('favorites.index');
    });

    Route::get('/ranking',[RankingController::class,'ranking'])->name('ranking.index');
});

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}',[BookController::class,'show'])->name('books.show');
