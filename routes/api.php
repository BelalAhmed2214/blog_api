<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;

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

Route::get("/",function(){
    return 'test';
});
Route::group(['prefix'=>"posts","middleware"=>"auth:api"],function(){
    Route::get("/deleted", [PostController::class,'deleted'])->name("deleted");
    Route::post("/restore/{post}",[PostController::class,'restore'] )->name("restore");
    Route::delete("/force_delete/{post}",[PostController::class,'forceDelete'] )->name("forceDelete");
});
Route::group(['prefix'=>"comments","middleware"=>"auth:api"],function(){
    Route::get("/deleted", [CommentController::class,'deleted'])->name("deleted");
    Route::post("/restore/{comment}",[CommentController::class,'restore'] )->name("restore");
    Route::delete("/force_delete/{comment}",[CommentController::class,'forceDelete'] )->name("forceDelete");
});

Route::group(["prefix"=>"auth"],function(){
    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);
    Route::post('logout',[AuthController::class,'logout']);
    Route::get('users',[AuthController::class,'getAllusers']);
    Route::get('profile',[AuthController::class,'profile']);
    Route::delete('delete_user/{user_id}',[AuthController::class,'deleteUser']);
    
});

Route::group(["middleware"=>"auth:api"],function(){
    Route::post('posts/search',[PostController::class,'searchByTitle'])->name('posts.search');
    Route::post('posts/filter',[PostController::class,'filteringSortingPosts'])->name('posts.filter');
    Route::post('posts/{post}',[PostController::class,'update'])->name('posts.update');
    Route::apiResource('posts', PostController::class)->except('update');
});

Route::group(["middleware"=>"auth:api"],function(){
    Route::post('comments/{comment}',[CommentController::class,'update'])->name('comments.update');
    Route::apiResource('comments', CommentController::class)->except('update');    
});
