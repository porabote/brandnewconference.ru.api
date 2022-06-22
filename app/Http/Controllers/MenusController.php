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

        if ($aro['parent_id'] == 1) {//debug(Auth::$user->id);
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

        return response()->json([
            'data' => $menusAllowed,
            'meta' => []
        ]);
    }

//    function addMass()
//    {
//        $aros = AclAros::get()->toArray();
//        foreach ($aros as $aro) {
//            $perm = AclPermissions::where('aro_id', $aro['id'])->where('aco_id', 43)->get()->toArray();
//            if (!$perm) {
//                $newPerm = [
//                    "aro_id" => $aro['id'],
//                    "aco_id" => 43,
//                    "_create" => 1,
//                    "_read" => 1,
//                    "_update" => 1,
//                    "_delete" => 1,
//                ];
//                AclPermissions::create($newPerm);
//            }
//        }
//    }

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