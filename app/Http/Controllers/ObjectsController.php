<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Objects;

class ObjectsController extends Controller
{
    use ApiTrait;

    function add(Request $request)
    {
        $data = $request->all();

        Objects::create($data);

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

}
