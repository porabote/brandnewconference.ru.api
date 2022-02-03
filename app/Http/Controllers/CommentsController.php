<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Http\Middleware\Auth;
use App\Models\Comment;

class CommentsController extends Controller
{
    use ApiTrait;
    
//    function add(Request $request)
//    {
//
//        $data = $request->all();
//
//        Comment::create($data);
//
//        return response()->json([
//            'data' => $data,
//            'meta' => []
//        ]);
//    }
}
