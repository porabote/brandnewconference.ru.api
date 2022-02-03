<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Porabote\Components\Auth\AuthException;
use Porabote\Auth\JWT;
use Porabote\Auth\Auth;
use App\Models\Users;
use App\Models\AclAcos;
use App\Models\AclAros;
use App\Models\AclPermissions;
use Porabote\FullRestApi\Server\ApiTrait;

class UsersController extends Controller
{

    use ApiTrait;

    private $authData = [];

    function check(Request $request)
    {
        $token = str_replace('JWT ', '', $request->header('Authorization'));

        return response()->json([
            'data' => [
                'account_alias' => 'porabote',
                'token' => $token
            ],
            'meta' => []
        ]);
    }

    function login(Request $request)
    {
        try {
            $this->authData = $request->all()['data'];

            $this->_login();
        } catch (\Porabote\Exceptions\AuthException $exception) {
            echo $exception->jsonApiError();
        }

    }

    function _login()
    {
        $user = $this->_identify();

        if ($user) {
            return response()->json([
                'data' => JWT::setToken($user),
                'meta' => []
            ]);
        }
    }

    function _identify()
    {
        if(!$this->authData) throw new \Porabote\Exceptions\AuthException('Auth data is empty');

        if(!isset($this->authData['username']) || !isset($this->authData['password'])) {
            throw new \Porabote\Exceptions\AuthException('Error of identify: some request data wasn`t recieved');
        }

        $user = Users::where('username', $this->authData['username'])->first();

        if(!$user) throw new \Porabote\Exceptions\AuthException('Error of identify: User not found');

        return $this->_authentificate($user);
    }

    function _authentificate($user)
    {
        if (!password_verify($this->authData['password'], $user->password)) {
            throw new \Porabote\Exceptions\AuthException('Authentificate error: the password is incorrect');
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

    function setToken(Request $request)
    {
        $data = $request->all();
        parse_str($data['data'], $user);

        $token = $this->_setToken($user['data']);

        return response()->json($token);

    }

    function _setToken($userDataExt)
    {
        $userData = [
            'id' => null,
            'username' => null,
            'name' => null,
            'last_name' => null,
            'post_id' => null,
            'account_alias' => null,
            'avatar' => null,
            'api_id' => null,
            'post_name' => null,
            'role_id' => null
        ];

        $data = array_intersect_key($userDataExt, $userData);

        return JWT::setToken($data);
    }

    function getAclLists($request)
    {
        $data = $request->all();

        $aro = AclAros::get()
            ->where('foreign_key', $data['user_id'])
            ->where('label', 'User')
            ->first()
        ->toArray();

        $acosList = AclAcos::orderBy('name', 'asc')->get();
        $permissions = collect(AclPermissions::get()
            ->where('aro_id', $aro['id'])
        )->keyBy('aco_id');

        return response()->json([
            'data' => [
                'acosList' => $acosList,
                'permissions' => $permissions,
                'aro' => $aro,
            ],
            'meta' => []
        ]);
    }

    function setPermission($request)
    {
        $user = Users::find(Auth::$user->id)->toArray();
        if ($user['role_id'] != 1) {
            return response()->json([
                'data' => [
                    'error' => ['Access denied'],
                ],
                'meta' => []
            ]);
        }

        $data = $request->all();

        if ($data['status']) {
            $this->addAccess($data['aco_id'], $data['aro_id']);
        } else {
            $this->deleteAccess($data['aco_id'], $data['aro_id']);
        }

    }

    function addAccess($aco_id, $aro_id)
    {
        $permission = AclPermissions::get()
            ->where('aco_id', $aco_id)
            ->where('aro_id', $aro_id)
            ->first();

        if (!$permission) {
            AclPermissions::create([
                'aco_id' => $aco_id,
                'aro_id' => $aro_id,
                '_create' => 1,
                '_read' => 1,
                '_update' => 1,
                '_delete' => 1,
            ]);
        }
    }

    function deleteAccess($aco_id, $aro_id)
    {
        $permission = AclPermissions::get()
            ->where('aco_id', $aco_id)
            ->where('aro_id', $aro_id)
            ->first();
       // $perm = AclPermissions::find($permission['id']);

        if ($permission) {
            $permission->delete();
        }
    }

}
