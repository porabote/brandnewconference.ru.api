<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posts;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $records = Posts::all();

        return [
            'data' => $records,
            'meta' => [
                'count' => count($records)
            ]
        ];
    }

    public function show($post)
    {
        return [
            'data' => Posts::find($post),
        ];
    }

    public function store(Request $request)
    {
        return Posts::create($request->all());
    }

    public function update(Request $request, $post)
    {
        $Posts = Posts::findOrFail($post);
        $Posts->update($request->all());

        return $Posts;
    }

    public function delete(Request $request, $post)
    {
        $Posts = Posts::findOrFail($post);
        $Posts->delete();

        return 204;
    }
}
