<?php
namespace App\Http\Responses;

class RestDataItem {

    public $id;
    public $type;
    public $attributes;
    public $relationships;
    public $links;

    public function __construct($data, $type, $url)
    {
        $this->id = $data['id'];
        $this->type = $type;
        $this->attributes = $data;
        $this->relationships = [];
        $this->links['self'] = $url . '/' . $data['id'];
    }

    function setRelationships($relationships)
    {
        $this->relationships = $relationships;
    }
}