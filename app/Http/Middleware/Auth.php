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

        $authHeader = $request->header('Authorization');
        $authHeaderSplits = explode(' ', $authHeader);

        if (isset($authHeaderSplits['1'])) {
            $user = $this->setUser($authHeaderSplits['1']);

            \Porabote\Auth\Auth::setUser($user);
        }

        return $next($request);
    }

    private function setUser($token)
    {
        $user = JWT::_decode($token);

        return $user;
    }

}
