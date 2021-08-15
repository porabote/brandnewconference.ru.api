<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reports;
use App\Models\File;
use App\Traits\ApiTrait;
use Porabote\Uploader\Uploader;

class ReportsController extends Controller
{
    use ApiTrait;

    function uploadReportFile(Request $request)
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

        return response()->json([
            'data' => $file,
            'meta' => []
        ]);

    }
}
