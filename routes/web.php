<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Porabote\FullRestApi\Server\ApiRouter;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{controller}/get/{id?}', [ ApiRouter::class, 'get' ]);
Route::get('/{controller}/{id}/relationships/{related_model}', [ ApiRouter::class, 'getRelationships' ]);
Route::post('/{controller}/add/', [ ApiRouter::class, 'add' ]);
Route::get('/{controller}/delete/{id}', [ ApiRouter::class, 'delete' ]);
Route::match(['GET', 'POST'], '/{controller}/method/{method}/', [ ApiRouter::class, 'executeCustomMethod' ]);
Route::match(['GET', 'POST'], '/{controller}/method/{method}/{id?}', [ ApiRouter::class, 'executeCustomMethod' ]);
//echo 289;exit();
Route::post('/files/upload/', [ App\Http\Controllers\FilesController::class, 'upload' ]);
Route::post('/users/setToken/', [ App\Http\Controllers\UsersController::class, 'setToken' ]);