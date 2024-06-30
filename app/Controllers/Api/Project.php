<?php

namespace App\Controllers\Api;

use App\Models\ProjectModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Project extends ResourceController
{
    use ResponseTrait;

    protected $projectModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
    }

    public function index()
    {
        //
    }
}