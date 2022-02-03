<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;
use App\Models\File;
use App\Models\History;

class FilesController extends Controller
{
    use ApiTrait;

    function upload(Request $request)
    {
        $data = $request->all();

        if (isset($data['files'])) {

            $files = [];

            foreach ($data['files'] as $item) {
                $File = $item['file'];
                unset($item['file']);
                $files[] = $this->uploadFile($File, $item);
            }

            return response()->json([
                'data' => $files,
                'meta' => []
            ]);

        }

    }

    function uploadFile($file, $fileInfo)
    {
        $file = Uploader::upload($file);

        $file = array_merge($file, $fileInfo);

        File::create($file);

        History::create([
            'model_alias' => 'reports',
            'record_id' => $file['record_id'],
            'msg' => 'Загружен файл: ' . $file['basename']
        ]);

        return $file;
    }

    function changeFileInfo(Request $request)
    {
        $data = $request->all();

        $file = File::find($data['id']);
        foreach ($data as $fieldName => $value) {
            $file->$fieldName = $value;
        }
        $file->update();

        return response()->json([
            'data' => $file,
            'meta' => []
        ]);
    }

    function markToDelete($request, $id)
    {
        $file = File::find($id);
        $file->flag = "to_delete";
        $file->update();

        return response()->json([
            'data' => $file,
            'meta' => []
        ]);
    }
}