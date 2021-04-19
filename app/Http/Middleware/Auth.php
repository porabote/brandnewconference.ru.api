<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        if($request->header('HTTP_X_REQUESTED_WITH') && !$request->header('Authorization')) {//!$request->header('HTTP_X_REQUESTED_WITH') ||
//
//            header('Content-Type: application/json');
//            return response()->json([
//                'errors' => [  [
//                    'status' => 401,
//                    'title' => 'You aren`t authorized.'
//                ]]
//            ], 401);
//        } else if($request->header('Authorization')) {
//
//        }

        return $next($request);
    }
}
