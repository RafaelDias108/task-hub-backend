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
                $project = $this->projectModel->select('uuid_project, fk_id_user, name_project, date_project, created_at')->where(['uuid_project' => $uuid, 'fk_id_user' => $this->user->id_user])->find();

                if (is_null($project) || empty($project)) {
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
                $projects = $this->projectModel->select('uuid_project, fk_id_user, name_project, date_project, created_at')->where('fk_id_user', $this->user->id_user)->findAll();

                if (is_null($projects) || empty($projects)) {
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
                    'status' => 'success',
                    'message' => "Projeto criado com sucesso",
                    'data' => $project
                ], 201);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => "Não foi possível criar o projeto",
                    'data' => [
                        'errors' => $this->projectModel->errors()
                    ]
                ], 400);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Erro na requisição: " . $error->getMessage(),
            ], 400);
        }
    }

    public function UpdateProject($uuid = null)
    {
        if (is_null($uuid)) {
            return $this->respond([
                'status' => "error",
                'message' => "UUID inválido",
            ], 400);
        }

        try {
            $project = $this->projectModel->where(['uuid_project' => $uuid, 'fk_id_user' => $this->user->id_user])->first();
            if (is_null($project)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Projeto não encontrado",
                    'data' => []
                ], 404);
            }

            $data = $this->request->getJSON();
            $isUpdated = $this->projectModel->update($project->id_project, $data);
            if ($isUpdated) {
                $projectUpdated = $this->projectModel->find($project->id_project);
                return $this->respond([
                    'status' => "success",
                    'message' => "Projeto atualizado",
                    'data' => $projectUpdated
                ], 200);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'status' => "error",
                'message' => "Erro ao atualizar o projeto: " . $error->getMessage()
            ], 400);
        }
    }

    public function DeleteProject($uuid = null)
    {
        if (is_null($uuid)) {
            return $this->respond([
                'status' => "error",
                'message' => "UUID inválido"
            ], 400);
        }

        try {

            $project = $this->projectModel->where(['uuid_project' => $uuid, 'fk_id_user' => $this->user->id_user])->find();

            if (is_null($project)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Projeto não encontrado",
                    'data' => []
                ], 404);
            }

            if ($this->projectModel->delete($project->id_project)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Projeto excluído com sucesso",
                    'data' => $project
                ], 200);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'status' => "error",
                'message' => "Ocorreu um erro ao deletar o projeto: " . $error->getMessage(),
                'data' => []
            ], 400);
        }
    }
}