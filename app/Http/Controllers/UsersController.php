<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Porabote\Components\Auth\AuthException;
use Porabote\Components\Auth\JWT;

class UsersController extends Controller
{

    private $authData = [];

    function login(Request $request)
    {
        try {
            //$this->authData = $request->all()['data'];
            $this->authData = [
                'username' => 'maksimov_den@mail.ru',
                'password' => 'z7893727'
            ];
            $this->_login();
        } catch (\App\Exceptions\AuthException $exception) {
            echo $exception->jsonApiError();
        }

    }

    function _login()
    {
        $user = $this->_identify();
//dd($user);
        JWT::setToken($user);
        //if ($user) $this->json(JWT::setToken($user));
    }

    function _identify()
    {
        if(!$this->authData) throw new \App\Exceptions\AuthException('Auth data is empty');

        if(!isset($this->authData['username']) || !isset($this->authData['password'])) {
            throw new \App\Exceptions\AuthException('Error of identify: some request data wasn`t recieved');
        }

        $user = Users::where('username', $this->authData['username'])->first();

        if(!$user) throw new \App\Exceptions\AuthException('Error of identify: User not found');

        return $this->_authentificate($user);
    }

    function _authentificate($user)
    {
        if (!password_verify($this->authData['password'], $user->password)) {
            throw new \App\Exceptions\AuthException('Authentificate error: the password is incorrect');
        }

        $userData = $user->getAttributes();
        unset($userData['password']);

        return $userData;
    }

    function registration()
    {
//        $user = new Users();
//        $user->password = Hash::make('z');
//        $user->username = 'd.razumihin@porabote.ru';
//        $user->name = 'Дмитрий';
//        $user->last_name = 'Разумихин';
//        $user->save();
    }
}
