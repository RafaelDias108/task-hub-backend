<?php

namespace App\Controllers\Api;

use App\Models\TaskModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Task extends ResourceController
{
    use ResponseTrait;

    protected $taskModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->taskModel = new TaskModel();
    }

    public function index()
    {

        // return $this->respond([
        //     'status'   => 200,
        //     'error'    => null,
        //     'messages' => [
        //         'success' => 'testando api'
        //     ]
        // ]);
    }
}