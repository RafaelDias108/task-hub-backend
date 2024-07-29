<?php

namespace App\Controllers\Api;

use App\Models\ProjectModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Project extends ResourceController
{
    use ResponseTrait;

    protected $projectModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
    }

    public function index()
    {
        try {
            return $this->respond([
                'success' => true,
                'message' => "Retorna todos os projetos do usuário",
                'data' => []
            ]);
        } catch (\Exception $error) {
            //throw $th;
        }
    }

    public function GetProjectByUID($uid)
    {
        try {
        } catch (\Exception $error) {
            //throw $th;
        }
    }
}