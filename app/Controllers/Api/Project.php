<?php

namespace App\Controllers\Api;

use App\Models\ProjectModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Project extends ResourceController
{
    use ResponseTrait;

    private $projectModel;
    private $userController;
    private $user;

    public function __construct()
    {
        helper('uuid_helper');
        $this->projectModel = new ProjectModel();
        $this->userController = new User();
        $this->user = $this->userController->GetUserByAccessToken();
    }

    public function index($uuid = false)
    {
        try {

            if ($uuid) {
                $project = $this->projectModel->select('uuid_project, fk_id_user, name_project, date_project, created_at')->where('uuid_project', $uuid)->find();
                // $project = $this->projectModel->where('uuid_project', $uuid)->find();
                if (is_null($project)) {
                    return $this->respond([
                        'status' => 'success',
                        'message' => "Projeto não encontrado",
                        'data' => []
                    ], 404);
                }

                return $this->respond([
                    'status' => 'success',
                    'data' => $project
                ], 200);
            } else {
                $projects = $this->projectModel->select('uuid_project, fk_id_user, name_project, date_project, created_at')->findAll();
                if (is_null($projects)) {
                    return $this->respond([
                        'status' => 'success',
                        'message' => "Projetos não encontrados",
                        'data' => []
                    ], 404);
                }
                return $this->respond([
                    'status' => 'success',
                    'data' => $projects
                ], 200);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Erro interno: " . $error,
                'data' => []
            ], 400);
        }
    }

    public function NewProject()
    {
        $projectData = $this->request->getJSON();
        $projectData->fk_id_user = intval($this->user->id_user);
        $projectData->uuid_project = GenerateUUID();

        try {

            if ($this->projectModel->insert($projectData)) {
                $project = $this->projectModel->find($this->projectModel->getInsertID());
                return $this->respond([
                    'success' => true,
                    'message' => "Projeto criado com sucesso",
                    'data' => $project
                ], 201);
            } else {
                return $this->respond([
                    'success' => false,
                    'message' => 'Erro ao criar o projeto',
                    'errors' => $this->projectModel->validation->getErrors()
                ], 400);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'success' => false,
                'message' => "Erro na requisição: " . $error->getMessage(),
            ], 400);
        }
    }
}