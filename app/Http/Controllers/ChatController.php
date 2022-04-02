<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\Uploader\Uploader;
use Porabote\Auth\Auth;
use App\Models\File;
use App\Models\Dialogs;
use App\Models\DialogsUsers;
use Porabote\FullRestApi\Server\ApiTrait;

class ChatController //extends ChatAbstractClass
{
    use ApiTrait;

    function __construct() {
        Auth::$user;
    }

    function setDialog($request)
    {
        $data = $request->all();

        if(!$this->isDialogIsset($data['userId'])) {

            Dialogs::create();

            DialogsUsers::create([

            ]);
        }

debug($data['userId']);
echo Auth::$user->id;
        //Comment::create($data);

//        Dialogs::get()
//            ->where();
    }

    function isDialogIsset()
    {

    }

    function getDialogs()
    {
    }

    function addDialog() {}
    function deleteDialog(){}

    function addMessage() {}
    function getMessages() {}

}
