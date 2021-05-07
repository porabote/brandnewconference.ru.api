<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ApiTrait
{
    private $modeName;

    function get()
    {
        $className = '\App\Models\\' . $this->getModelName();

        if(class_exists($className)) {
            $query = $className::where('id', '>', 0);
        } else {
            $query = DB::table(strtolower($this->getModelName()));
        }


        return response()->json([
            'data' => $query->limit(50)->get(),
            'meta' => []
        ]);
    }

    function getModelName()
    {
        preg_match('/([a-z]+)Controller$/i', get_class(), $modelName);
        return $modelName[1];
    }
}


?>