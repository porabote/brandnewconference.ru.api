<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/porabote/tests/event/', [ App\Http\Controllers\PoraboteController::class, 'event' ]);
Route::get('/mailer/sendTest/', [ App\Http\Controllers\MailerController::class, 'sendTest' ]);
Route::get('/api-users/update/', [ App\Http\Controllers\ApiUsersController::class, 'update' ]);