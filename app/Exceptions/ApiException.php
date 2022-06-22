<?php
namespace App\Exceptions;

class ApiException extends \Exception
{

    public function jsonApiError()
    {
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(['error' => $this->getMessage()]));
    }

    public function toJSON()
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => $this->getMessage()]);
    }

}
?>