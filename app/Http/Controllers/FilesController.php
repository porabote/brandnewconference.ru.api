<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiTrait;
use Porabote\Uploader\Uploader;
use App\Http\Components\HistoryComponent;
use App\Models\Menus;
use App\Models\File;
use App\Models\History;

class FilesController extends Controller
{
    use ApiTrait;

    function upload(Request $request)
    {
        $file = Uploader::upload($request->file());

        foreach ($request->all() as $field => $value) {
            if (is_string($value)) {
                $file[$field] = $value;
            }
        }

        $File = new File($file);

        foreach ($file as $field => $value) {
            $File->$field = $value;
        }

        $File->save();

        History::create([
            'model_alias' => 'reports',
            'record_id' => $File->record_id,
            'msg' => 'Загружен файл: ' . $File->basename,
//            'user_id' => '99',
//            'user_name' => 111
        ]);

        return response()->json([
            'data' => $file,
            'meta' => []
        ]);

    }
}