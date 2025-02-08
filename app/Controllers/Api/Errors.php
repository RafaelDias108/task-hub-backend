<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Errors extends ResourceController
{
    use ResponseTrait;

    public function NotFound()
    {
        return $this->respond([
            'status'   => 'error',
            'message' => 'Recurso não encontrado',
        ], 404);
    }

    public function Unauthorized()
    {
        return $this->respond([
            'status'   => 'error',
            'message' => 'Não autorizado',
        ], 401);
    }
}