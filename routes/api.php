<?php

use App\Http\Controllers\Api\V1\BookApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function(){
    Route::middleware('auth:sanctum')->group(function(){
        Route::apiResource('books',BookApiController::class)->only(['store','update','destroy']);
    });
    Route::post('login',[LoginController::class,'login']);
});
