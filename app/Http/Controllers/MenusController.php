<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Menus;
use App\Models\AclAcos;
use App\Models\AclAros;
use App\Models\AclPermissions;
use Porabote\Auth\Auth;

class MenusController extends Controller
{

    use ApiTrait;

    function get()
    {
        $tree = $this->getDepth();

        ob_clean();
        return response()->json([
            'data' => $tree,
            'meta' => []
        ]);
    }


    function getByAcl()
    {
        $menus = $this->getDepth();
        $aro = AclAros::where('foreign_key', Auth::$user->id)
            ->where('label', "User")
            ->get()
            ->first()
        ->toArray();

      //  if ($aro['id'] == 5) $aro['parent_id'] = 4;

        if ($aro['parent_id'] == 1) {
            return response()->json([
                'data' => $menus,
                'meta' => []
            ]);
        }

        $aro_id = $aro['id'];

     //   if ($aro_id == 5) $aro_id = 258;

        $perms = AclPermissions::where('aro_id', $aro_id)->get()->toArray();
        $permsList = [];
        foreach ($perms as $perm) {
            $permsList[$perm['aco_id']] = $perm['aco_id'];
        }

        $menusAllowed = [];
        foreach ($menus as $menu) {
            if (isset($permsList[$menu->aco_id])) {
                $menusAllowed[$menu->lft] = $menu;
            }
        }
//$this->addMass();
        return response()->json([
            'data' => $menusAllowed,
            'meta' => []
        ]);
    }

    function addMass()
    {// 10 37 22 1 43
        $aros = AclAros::get()->toArray();
        foreach ($aros as $aro) {
            $perm = AclPermissions::where('aro_id', $aro['id'])->where('aco_id', 43)->get()->toArray();
            if (!$perm) {
                $newPerm = [
                    "aro_id" => $aro['id'],
                    "aco_id" => 43,
                    "_create" => 1,
                    "_read" => 1,
                    "_update" => 1,
                    "_delete" => 1,
                ];
                AclPermissions::create($newPerm);
            }
        }
    }

    function getByAcl_TMP()
    {
        $tree = $this->getDepth();

        // Заливаем ACOS
//        foreach ($tree as $item) {
//
//            $aco = [
//                'name' => $item->name,
//                'foreign_key' => $item->id,
//                'parent_id' => null,
//                'model' => 'App\Models\Menus'
//            ];
//            $aco = AclAcos::create($aco);
//
//            debug($item);
//            $menuUpRecord = Menus::find($item->id);
//            $menuUpRecord->aco_id = $aco['id'];
//            $menuUpRecord->update(['aco_id' => $aco['id']]);
//            $menuUpRecord->save();
//        }

        // Заливаем AROS
//        $acl_aros = AclAros::get();
//
//        $aros = DB::connection('Solikamsk_mysql')->table('aros')->get()->toArray();
//        foreach ($aros as $aro) {
//
//            $newAro = [
//                'id' => $aro->id,
//                'label' => $aro->alias,
//                'parent_id' => $aro->parent_id,
//                'foreign_key' => $aro->foreign_key,
//                'model' => "App\Models\Users",
//            ];
//
//            AclAros::create($newAro);
//        }

        //Заливаем пермишены

//        foreach ($tree as $menu) {
//            debug($menu);
//
////            $aco_parent = TableRegistry::get('Acos')
////                ->find()
////                ->where(['Acos.alias' =>  $aco['controller'], 'Acos.root_node' => $aco['plugin'], 'parent_id' => 1])
////                ->first();
//            break;
//        }

//        $acos = DB::connection('Solikamsk_mysql')->table('acos')->whereNotNull('title')->get()->toArray();
//foreach ($acos as $aco) {
//
//    $aco->parent = DB::connection('Solikamsk_mysql')->table('acos')->where('id', $aco->parent_id)->get()->toArray();
//    debug($aco);
//}

        $acosMap = [];
        $acosList = [];
        foreach (AclAcos::get()->toArray() as $menu) {//debug($menu);
            if ($menu['old_id']) {
                $acosMap[$menu['old_id']] = $menu['id'];
                $acosList[$menu['old_id']] = $menu['old_id'];
            }
        }
//debug($acosMap);
//        debug($acosList);
        $permissions = DB::connection('Solikamsk_mysql')->table('aros_acos')->whereIn('aco_id', $acosList)->get()->toArray();
        $i = 0;
        foreach ($permissions as $permission) {
           // debug($permission);
            $newPerm = [
                "aro_id" => $permission->aro_id,
                "aco_id" => $acosMap[$permission->aco_id],
                "_create" => 1,
                "_read" => 1,
                "_update" => 1,
                "_delete" => 1,
            ];
            $i++;
//debug($newPerm);
            AclPermissions::create($newPerm);
           // if ($i == 10) break;
        }

    }

    function getDepth()
    {
        $query = 'SELECT * ,
                       (
                           SELECT COUNT(id) FROM menus
                           WHERE lft < node.lft
                             AND rght > node.rght
                             AND node.lft <> 0
                       ) AS depth
                    FROM `menus` AS node
                    WHERE lft <> 0 AND flag="on" ORDER BY lft';
        $menus = DB::connection('auth_mysql')->select($query);

        $output = [];
        foreach  ($menus as $menu) {
            $output[$menu->lft] = $menu;
        }
        return $output;
    }

}