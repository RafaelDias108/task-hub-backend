<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Errors extends ResourceController
{
    use ResponseTrait;

    public function NotFound()
    {
        return $this->failNotFound();
    }

    public function Unauthorized()
    {
        return $this->failUnauthorized();
    }
}