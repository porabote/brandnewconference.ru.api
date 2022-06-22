<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Porabote\Auth\JWT;
use Porabote\Auth\AuthException;

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
        try {
            $authHeader = $request->header('Authorization');

            $authHeaderSplits = explode(' ', $authHeader);
            $token = isset($authHeaderSplits['1']) ? $authHeaderSplits['1'] : null;

            if ($token) {
                $user = JWT::_decode($token);//$this->getUser($token);
                \Porabote\Auth\Auth::setUser($user);
            } else {
                if ($isNeedsAllows = self::checkAllows($request)) {
                    throw new \Porabote\Auth\AuthException('You are not authorized.');
                }
            }
        } catch (\Porabote\Auth\AuthException $e) {
            $e->jsonApiError();
        }

        return $next($request);
    }

    private static function checkAllows($request)
    {
        $uri = explode('/', str_replace('/api/', '', $request->getPathInfo()));

        $controllerAlias = '\App\Http\Controllers\\' . \Porabote\Stringer\Stringer::snakeToCamel($uri[0]) . 'Controller';
        $methodAlias = $uri[1];
        if ($methodAlias == "method") {
            $methodAlias = $uri[2];
        }

        if (!class_exists($controllerAlias)) {
            throw new \Porabote\Auth\AuthException('Class doesn`t exists.');
        } else {
            $controller = new $controllerAlias();
            if (
                property_exists($controllerAlias, "authAllows")
                && in_array($methodAlias, $controller::$authAllows)
            ) {
                return false;
            }
        }
        return true;
    }

//    private function getUser($token)
//    {
//        return JWT::_decode($token);
//    }

}
