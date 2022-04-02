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

class DialogsController //extends ChatAbstractClass
{
    use ApiTrait;

    function createDialog($request)
    {
        $data = $request->all();

        $dialog = Dialogs::create();

        DialogsUsers::create([
            'dialog_id' => $dialog->id,
            'user_id' => $data['userId'],
        ]);
        DialogsUsers::create([
            'dialog_id' => $dialog->id,
            'user_id' => Auth::$user->id,
        ]);

    }

    function getDialogs()
    {
    }

    function addDialog() {}
    function deleteDialog(){}

}
