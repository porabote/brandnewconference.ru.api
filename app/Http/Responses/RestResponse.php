<?php
namespace App\Http\Responses;

use Illuminate\Http\Request;

class RestResponse {

    private $type;
    private $url;
    public $data = [];
    public $meta = [];

    function __construct(Request $request) {
        $pathSplited = explode('/', str_replace('api/', '', $request->path()));
        $this->type = $pathSplited[0];
        $this->url = $request->url();
    }

    function setData(RestDataItem $item)
    {
        array_push($this->data, $item);
    }
}

?>