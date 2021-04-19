<?php

namespace App\Exceptions;

class AuthException extends \Exception
{

    function jsonApiError($code = 403)
    {
        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode([
            'errors' => [  [
                'status' => (!$this->getCode()) ? 403 : $this->getCode(),
                'title' =>  $this->getMessage()
            ]]
        ]);
        
        http_response_code(403);
    }

}