<?php

interface Api {

    function get();
    function add();
    function getRelationship();
}

class ApiTrait implements Api {
    function get()
    {

    }

    function add()
    {

    }
    function getRelationship()
    {

    }
}

class ApiP extends ApiTrait
{

}

?>