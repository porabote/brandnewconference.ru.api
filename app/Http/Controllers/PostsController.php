<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posts;

class PostsController extends Controller
{
    function migration()
    {
        $user = new \stdClass();
        $user->account_alias = 'Thyssen';
        \Porabote\Auth\Auth::setUser($user);

        $apiUsers = \App\Models\ApiUsers::get()->toArray();
        $apiUsersList = [];
        $apiUsersListFull = [];
        foreach ($apiUsers as $apiUser) {
            $apiUsersList[$apiUser['email']] = $apiUser['id'];
            $apiUsersListFull[$apiUser['id']] = $apiUser;
        }
        //debug($apiUsersList);

        $posts = \App\Models\Posts::get()->toArray();
        $postsList = [];
        foreach ($posts as $post) {

            if (!isset($apiUsersList[$post['email']])) debug($post['email']);

            $newId = (isset($apiUsersList[$post['email']])) ? $apiUsersList[$post['email']] : '';
            $postsList[$post['id']] = $newId;
        }
        //debug($postsList);

        $components = [
            'Payments',
            'ApiUsersWidgets',
            'Purchases',
            'PurchaseRequest',
            'Certificates',
            'Users',
            'BusinessRequests',
            'Bills'
        ];

        foreach ($components as $component) {
            $className = '\App\Models\\' . $component;
            $records = $className::limit(1)->get();
            //debug($records->toArray());
        }

        // Бизнес запросы
        $br = \App\Models\BusinessRequests::limit(1)->get();
        foreach ($br as $b) {
            $data = json_decode($b->data_json, true);
            $newId = $postsList[$data['post']['id']];
            $data['post_id'] = $newId;
            $data['post'] =  $apiUsersListFull[$newId];

            $acceptance_scheme = json_decode($b->acceptance_scheme, true);
           // debug($acceptance_scheme);
        }

        //счета
        $bills = \App\Models\Bills::limit(1)->get();
        foreach ($bills as $bill) {
            $shema_handle = unserialize($bill->shema_handle);
//            $data = json_decode($b->data_json, true);
//            $newId = $postsList[$data['post']['id']];
//            $data['post_id'] = $newId;
//            $data['post'] =  $apiUsersListFull[$newId];

            $acceptance_scheme = json_decode($b->acceptance_scheme, true);
            // debug($acceptance_scheme);
        }

        // запросы
        //счета
        $purchasesRequests = \App\Models\PurchaseRequest::limit(1)->get();
        foreach ($purchasesRequests as $purchasesRequest) {

            $members = unserialize($purchasesRequest->members);
            foreach ($members as $member) {
                debug($member);
            }
//            $data = json_decode($b->data_json, true);
//            $newId = $postsList[$data['post']['id']];
//            $data['post_id'] = $newId;
//            $data['post'] =  $apiUsersListFull[$newId];

            $acceptance_scheme = json_decode($b->acceptance_scheme, true);
            // debug($acceptance_scheme);
        }





    }

//    public function index(Request $request)
//    {
//        $records = Posts::all();
//
//        return [
//            'data' => $records,
//            'meta' => [
//                'count' => count($records)
//            ]
//        ];
//    }
//
//    public function show($post)
//    {
//        return [
//            'data' => Posts::find($post),
//        ];
//    }
//
//    public function store(Request $request)
//    {
//        return Posts::create($request->all());
//    }
//
//    public function update(Request $request, $post)
//    {
//        $Posts = Posts::findOrFail($post);
//        $Posts->update($request->all());
//
//        return $Posts;
//    }
//
//    public function delete(Request $request, $post)
//    {
//        $Posts = Posts::findOrFail($post);
//        $Posts->delete();
//
//        return 204;
//    }
}
