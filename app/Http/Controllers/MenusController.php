<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiTrait;
use App\Models\Menus;

class MenusController extends Controller
{

    function get()
    {
        $tree = $this->getDepth();

        ob_clean();
        return response()->json([
            'data' => $tree,
            'meta' => []
        ]);
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
                    WHERE lft <> 0 ORDER BY lft';
        $menus = DB::connection('auth_mysql')->select($query);

        $output = [];
        foreach  ($menus as $menu) {
            $output[$menu->lft] = $menu;
        }
        return $output;
    }

}