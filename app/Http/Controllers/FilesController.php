<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\File;

class FilesController extends Controller
{

    function upload(Request $request)
    {
        debug($request->file());

        $fileModel = new File;

        $fileName = $request->file->getClientOriginalName() . '_' . time();
        $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

        $fileModel->basename = $fileName;
        $fileModel->path = '/storage/' . $filePath;
        $fileModel->save();

        return response()->json($_POST);
    }

}
