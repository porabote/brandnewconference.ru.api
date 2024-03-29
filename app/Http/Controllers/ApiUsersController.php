<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Users;
use App\Models\ApiUsers;
use App\Http\Components\AccessLists;

class ApiUsersController extends Controller
{
    use ApiTrait;

    static function update()
    {
        $users = Users::leftJoin('posts', 'users.post_id', '=', 'posts.id')
            ->select([
                    '*',
                    'users.id as id',
                    'users.name as name',
                    'posts.name as post_name',
                ]
            )
            ->get()
            ->toArray();

        foreach ($users as $user) {
            $api_user = self::_checkAndAddUser($user);

            if ($api_user) {
                $user_local = Users::find($user['id']);
                $user_local->api_id = $api_user['id'];

                $user_local->save();

            }
        }
    }

    static function _checkAndAddUser($user_local)
    {

        $userApi = DB::connection('auth_mysql')
            ->table('users')
            ->where('email', '=', $user_local['username'])
            ->first();

        if ($userApi === null) {
            $user = ApiUsers::create([
                'email' => $user_local['username'],
                'name' => $user_local['fio'],
                'post_name' => $user_local['post_name']
            ]);
            return $user->getAttributes();
        } else {
            return (array)$userApi;
        }
    }

    function _create($data)
    {
        $ApiUser = self::_checkAndAddUser([
            'post_name' => $data['post_name'],
            'username' => $data['username'],
            'fio' => $data['last_name'] . ' ' . $data['name'],
        ]);

        return $ApiUser;
    }

    function checkEditAccess()
    {
        $isCanEdit = AccessLists::_check(11);
        $isCanViewTabs = AccessLists::_check(12);

        return response()->json([
            'data' => [
                'isCanEdit' => $isCanEdit,
                'isCanViewTabs' => $isCanViewTabs,
            ],
            'meta' => []
        ]);
    }
}