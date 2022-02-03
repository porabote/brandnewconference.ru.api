<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ObserversDefault;
use App\Models\Observers;
use App\Models\BusinessEvents;
use Porabote\FullRestApi\Server\ApiTrait;

class ObserversController extends Controller
{

    use ApiTrait;

//    function subscribeToAllByComponent(Request $request)
//    {
//        $data = $request->all();
//
//        $event_ids = BusinessEvents::where('component_id', 1)->pluck('id');
//
//        foreach ($data['users'] as $user_id) {
//            foreach ($event_ids as $event_id) {
//                self::_subscribe($user_id, $event_id, $data['entity_id']);
//            }
//        }
//    }

    static function subscribeByDefaultList($event_ids = [], $entity_id)
    {
        $observersDefault = ObserversDefault::whereIn('business_event_id', $event_ids)->get()->toArray();

        foreach ($observersDefault as $observer) {

            foreach ($event_ids as $event_id) {
                self::_subscribe($observer['user_id'], $event_id, $entity_id);
            }

        }
    }

    function subscribe($request)
    {
        $data = $request->query();

        foreach ($data['user_ids'] as $user) {
            foreach ($data['event_ids'] as $event) {
                $subscribes = $this->_subscribe($user, $event, $data['entity_id']);
            }
        }

        return response()->json([
            'data' => $subscribes,
            'meta' => []
        ]);
    }
    static function _subscribe($user_id, $event_id, $entity_id)
    {
        $entity = Observers::where('entity_id', $entity_id)
            ->where('user_id', $user_id)
            ->where('business_event_id', $event_id)
            ->first();

        if ($entity === null) {
            Observers::create([
                'entity_id' => $entity_id,
                'user_id' => $user_id,
                'business_event_id' => $event_id,
            ]);
        }
    }

    function unsubscribe($request)
    {
        $data = $request->query();

        foreach ($data['user_ids'] as $user) {
            foreach ($data['event_ids'] as $event) {
                $subscribes = $this->_unsubscribe($user, $event, $data['entity_id']);
            }
        }

        return response()->json([
            'data' => $subscribes,
            'meta' => []
        ]);
    }

    private function _unsubscribe($user_id, $event_id, $entity_id)
    {
        $subscribes = Observers::where('user_id', $user_id)
            ->where('entity_id', $entity_id)
            ->where('business_event_id', $event_id)
            ->get()
            ->toArray();

        foreach ($subscribes as $subscribe) {
            Observers::destroy($subscribe['id']);
        }

        return $subscribes;
    }

//    function getSubscribesByComponentId($request, $id)
//    {
//        $data = $request->all();
//
//        $event_ids = BusinessEvents::where('component_id', $data['component_id'])->pluck('id');
//
//        $subscribes = Observers::where('user_id', $data['user_id'])
//            ->where('entity_id', $data['entity_id'])
//            ->whereIn('business_event_id', $event_ids)
//            ->get()
//        ->toArray();
//
//        return response()->json([
//            'data' => $subscribes,
//            'meta' => []
//        ]);
//    }

}