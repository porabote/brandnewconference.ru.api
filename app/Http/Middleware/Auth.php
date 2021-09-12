<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Porabote\Auth\JWT;

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
        \Porabote\Auth\Auth::setUser($this->setUser());

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

    function setUser($auth_header_string = null)
    {
        $auth_header_string = 'Authorization: bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjE0IiwidXNlcm5hbWUiOiJtYWtzaW1vdl9kZW5AbWFpbC5ydSIsIm5hbWUiOiJcdTA0MWNcdTA0MzBcdTA0M2FcdTA0NDFcdTA0MzhcdTA0M2NcdTA0M2VcdTA0MzIgXHUwNDE0XHUwNDM1XHUwNDNkXHUwNDM4XHUwNDQxIiwiYWNjb3VudF9hbGlhcyI6IlRoeXNzZW4iLCJhdmF0YXIiOiJcL3VzZXJmaWxlc1wvZmlsZXNcL2NsX1RoeXNzZW5cLzE0XC9wcm9maWxlXC8xNFwvYXZhdGFyXC8xNTE5NDIwODIzMTIxODI5NTc1LTEuanBnIiwiaWF0IjoxNjMxNDQ4MzMyLCJleHAiOjE2NjI5ODQzMzJ9.DLgY0LkLP-re9jtqRa3ntX9MskhyKOyV8TsFfnlaKEI';

        $token = substr($auth_header_string, 22 );
        $token = JWT::_decode($token);

        return $token;
    }
}
