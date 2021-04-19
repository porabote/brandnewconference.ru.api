<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\PostsController;
Use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('users/login', [ UsersController::class, 'login' ]);
Route::post('users/login', [ UsersController::class, 'login' ]);
Route::get('users/registration', [ UsersController::class, 'registration' ]);

Route::get('posts', [ PostsController::class, 'index' ]);
Route::get('posts/{post}', [ PostsController::class, 'show' ]);
Route::post('posts', 'PostsController@store');
Route::put('posts/{post}', 'PostsController@update');
Route::delete('posts/{post}', 'PostsController@delete');

?>