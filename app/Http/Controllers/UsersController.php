<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Porabote\Components\Auth\AuthException;
use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use Porabote\Auth\JWT;
use Porabote\Auth\Auth;
use App\Models\Users;
use App\Models\UsersRequests;
use App\Models\ApiUsers;
use App\Models\AclAcos;
use App\Models\AclAros;
use App\Models\AclPermissions;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Exceptions\ApiException;
use Porabote\Curl\Curl;
use App\Http\Components\AccessLists;

class UsersController extends Controller
{
    use ApiTrait;

    static $authAllows;
    private $authData = [];

    function __construct()
    {
        self::$authAllows = [
            'login',
            'setToken',
            'confirmInvitation',
            'sendInvitationNotification',
        ];
    }

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
            $data = $request->all();//debug($data);exit();
//            $data = [
//                'username' => 'maksimov_den@mail.ru',
//                'password' => 'z7893727',
//                'account_alias' => 'dentsu',
//            ];

            $user = Auth::identify(
                $data['username'],
                $data['password'],
                'dentsu'//$data['account_alias'],
            );

            return response()->json([
                'data' => [
                    //'user' => $user,
                    'jwtToken' => $this->_setToken($user)
                ],
                'meta' => []
            ]);

        } catch (\Porabote\Auth\AuthException $exception) {
            echo $exception->jsonApiError();
        }

    }


    function _setToken($userDataExt)
    {
        $userData = [
            'id' => null,
            'email' => null,
            'name' => null,
            'post_id' => null,
            'account_id' => null,
            'account_alias' => null,
            'avatar' => null,
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
        $user = ApiUsers::find(Auth::$user->id)->toArray();
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

    function create($request)
    {
        try {
            $data = $request->all();

            if (!$access = AccessLists::_check(11)) {
                throw new ApiException('Извините, Вам не выданы права для добавления пользователя.');
            }
            
            $user = self::_createUser($data);
            $aro = self::_createAro($user->id);

            $this->_setPermissionsByDefault($aro->id);

            return response()->json([
                'data' => $user,
                'meta' => []
            ]);

        } catch (ApiException $e) {
            $e->toJSON();
        }

    }

    private function _setPermissionsByDefault($aroId)
    {
        $acos = [1, 20, 43, 32, 37];
        foreach ($acos as $aco) {
            $this->addAccess($aco, $aroId);
        }
    }

    function edit($request)
    {
        try {

            $data = $request->all();

            if ($data['id'] != Auth::$user->id && !$access = AccessLists::_check(11)) {
                throw new ApiException('Извините, Вам не выданы права для редактирования пользователя.');
            }

            $user = ApiUsers::find($data['id']);

            $allowedList = array_flip(ApiUsers::$allowed_attributes);

            foreach ($data as $field => $value) {
                if (array_key_exists($field, $allowedList)) {
                    $user->$field = $value;
                }
            }

            $user->update();

            return response()->json([
                'data' => $user->toArray(),
                'meta' => []
            ]);

        } catch (ApiException $e) {
            $e->toJSON();
        }
    }

    private function _createAro($user_id)
    {
        $aro = AclAros::create([
            'parent_id' => null,
            'label' => 'User',
            'foreign_key' => $user_id,
            'model' => 'App\Models\Users',            
        ]);

        return $aro;
    }

    private static function _createUser($data)
    {
        $user = ApiUsers::get()
            ->where('email', $data['email'])
            ->first();

        if (!$user) {

            $user = [
                'email' => $data['email'],
                'name' => $data['last_name'] . ' ' . $data['name'],
                'last_name' => $data['last_name'],
                'patronymic' => $data['patronymic'],
                'post_name' => $data['post_name'],
                'department_id' => $data['department_id'],
                'status' => 'invited',
                'token' => self::createToken(),
                'password' => null,
                'role_id' => 2,
            ];
            return ApiUsers::create($user);
        } else {
            throw new ApiException('Пользователь с логином ' . $data['email'] . ' уже существует');
        }
    }

    function createUserRequest($request)
    {
        $data = $request->all();
        $request = self::_createUserRequest($data['user_id']);

        return response()->json([
            'data' => $request,
            'meta' => []
        ]);
    }

    static function _createUserRequest($user_id)
    {
        return UsersRequests::create([
            'user_id' => $user_id,
            'sender_id' => Auth::$user->id,
            'token' => self::createToken(),
            //'date_request' => \Carbon\Carbon::now(),
            'account_id' => Auth::$user->account_id,
        ]);
    }

    function sendInvitationNotification($Request, $requestId = 1)
    {
        $msgData = UsersRequests::with('user')
            ->with('sender')
            ->find($requestId)
            ->toArray();


        $user = new \stdClass();
        $user->account_alias = 'Thyssen';//Thyssen   Solikamsk
        \Porabote\Auth\Auth::setUser($user);

        $message = new Message();
        $message->setData($msgData)->setTemplateById(9);
        Mailer::setTo($msgData['user']['email']);
        Mailer::send($message);

        return response()->json([
            'data' => $msgData,
            'meta' => []
        ]);
    }

    function confirmInvitation($request)
    {
        try {
            $data = $request->all();

            $request = UsersRequests::with('user')
                ->with('sender')
                ->find($data['requestId']);

            if (!isset($request->token) || $request->token != $data['token']) {
                throw new ApiException('Извините, токен уже был использован.');
            }

            $userRequest = $request->toArray();

            if(!isset($data['password'])) {

                self::_checkRequestDate($userRequest['date_request']);

                return response()->json([
                    'data' => $userRequest,
                    'meta' => []
                ]);
            } else {
                self::_changePassword($userRequest['user_id'], $data['password'], $data['password_confirm']);

                $request->date_confirm = \Carbon\Carbon::now();
                $request->token = null;
                $request->update();

                return response()->json([
                    'data' => [],
                    'meta' => []
                ]);
            }
        } catch (ApiException $e) {
            $e->toJSON();
        }
    }

    static function _changePassword($user_id, $password, $password_confirm)
    {
        self::_checkPassword($password, $password_confirm);

        $hash = Hash::make($password);

        $user = ApiUsers::find($user_id);

        if (!$user) throw new ApiException('Пользователь не задан или не найден.');

        $user->password = $hash;
        $user->status = 'active';
        $user->update();
    }

    static function _checkPassword($password, $password_confirm)
    {
        if ($password != $password_confirm) {
            throw new ApiException('Пароли не совпадают.');
        } elseif (strlen($password) < 4) {
            throw new ApiException('Пароль не может быть менее 4 символов.');
        }
    }

    static function _checkRequestDate($date)
    {
        $dateDeadline = (new \DateTime($date))->modify('+3 day');
        $dateNow = new \DateTime();

        if ($dateDeadline < $dateNow) {
            throw new ApiException('Извините, с момента приглашения прошло более 3х дней, срок токена истек.');
        }
    }

    static function createToken()
    {
        $token = openssl_random_pseudo_bytes(16);
        $token = bin2hex($token);
        return $token;
    }













    function migrationPosts()
    {
        $user = new \stdClass();
        $user->account_alias = 'Thyssen';//Thyssen   Solikamsk
        \Porabote\Auth\Auth::setUser($user);


        $apiUsers = \App\Models\ApiUsers::get()->toArray();
        $apiUsersList = [];
        $apiUsersListFull = [];
        $apiUsersListFullByOld = [];
        foreach ($apiUsers as $apiUser) {

            if($apiUser['local_id']) $apiUsersListFullByOld[$apiUser['local_id']] = $apiUser['id'];

            $apiUsersList[$apiUser['email']] = $apiUser['id'];
            $apiUsersListFull[$apiUser['id']] = $apiUser;

        }

        $posts = \App\Models\Posts::get()->toArray();
        $postsList = [];
        foreach ($posts as $post) {
            if (!isset($apiUsersList[$post['email']])) debug($post['email']);
            $newId = (isset($apiUsersList[$post['email']])) ? $apiUsersList[$post['email']] : '';
            $postsList[$post['id']] = $newId;
        }

//        $users = \App\Models\ApiUsers::get();
//        foreach($users as $user) {
//            if (isset($postsList[$user['email']])) {
//                $user->phone = $postsList[$user['email']];
//                $user->update();
//            }
//        }


//          $files = \App\Models\Files::where('date_created', '<', '2022-05-14 11:51:01')
//              ->orderBy('id', 'desc')
//              ->with('user')
//            //  ->limit(10)
//              ->get();
//       // ini_set('memory_limit', '1256M');
//foreach ($files as $file) {
//
//    if(isset($apiUsersList[$file['user']['username']])) {
//        $userId = $apiUsersList[$file['user']['username']];
//        $file->user_id = $userId;//debug($userId);
//       // $file->save();
//    }
//    //$file->user_id =
//}
//        foreach ($payments as $payment) {
//
//           // $payment['post_id'] = $postsList[$payment['post_id']];
//            $payment['sender_id'] = $postsList[$payment['sender_id']];
//           // debug($payment['id']);
//           // debug($payment['acceptor_id']);
//           // $payment->update();
//        }

//        $payments = \App\Models\PaymentsSets::where('id', '<', 376)->get();//9108 //9042
//        foreach ($payments as $payment) {
//
//           // $payment['post_id'] = $postsList[$payment['post_id']];
//            $payment['sender_id'] = $postsList[$payment['sender_id']];
//           // debug($payment['id']);
//           // debug($payment['acceptor_id']);
//           // $payment->update();
//        }


//        $nomencl = \App\Models\PurchaseNomenclatures::get();
//        foreach($nomencl as $nmcl) {
//            if (!$nmcl['manager_id']) continue;
//            if(!isset($postsList[$nmcl['manager_id']])) echo $nmcl['id'];
//            $nmcl['manager_id'] = $postsList[$nmcl['manager_id']];
//            //$nmcl->update();
//        }

//        $configs = \App\Models\Configs::find(12);
//        $value = unserialize($configs['value']);
//        $newValues = [];
//
//        foreach ($value as $val) {
//            if (!isset($postsList[$val])) continue;
//            $newValues[$postsList[$val]] = $postsList[$val];
//        }
//       // debug($newValues);
//        $configs->value = serialize($newValues);
//       // $configs->update();

    }
}
