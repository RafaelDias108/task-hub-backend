<?php

namespace App\Controllers\Api;

use App\Models\CategoryModel;
use App\Models\ProjectCategoryModel;
use App\Models\ProjectModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Exception;

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
        $projectCategoryModel = new ProjectCategoryModel();

        try {

            if ($uuid) {
                $project = $this->projectModel->select('id_project, uuid_project, fk_id_user, name_project, date_project, created_at')->where(['uuid_project' => $uuid, 'fk_id_user' => $this->user->id_user])->first();

                if (is_null($project) || empty($project)) {
                    return $this->respond([
                        'status' => 'success',
                        'message' => "Projeto não encontrado",
                        'data' => []
                    ], 404);
                }

                $categories = $projectCategoryModel->select('tb_category.*')->join('tb_category', 'tb_category.id_category = tb_project_category.id_category')->where(['tb_project_category.id_project' => $project->id_project])->findAll();

                $project->categories = $categories;

                return $this->respond([
                    'status' => 'success',
                    'data' => $project
                ], 200);
            } else {
                $projects = $this->projectModel->where('fk_id_user', $this->user->id_user)->findAll();

                if (is_null($projects) || empty($projects)) {
                    return $this->respond([
                        'status' => 'success',
                        'message' => "Projetos não encontrados",
                        'data' => []
                    ], 404);
                }

                foreach ($projects as $project) {
                    $project->categories = $projectCategoryModel->select('tb_category.*')->join('tb_category', 'tb_category.id_category = tb_project_category.id_category')->where(['tb_project_category.id_project' => $project->id_project])->findAll();
                }

                return $this->respond([
                    'status' => 'success',
                    'data' => $projects
                ], 200);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível buscar projetos: " . $error->getMessage(),
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

    public function LinkCategoryToProject()
    {
        $data = $this->request->getJSON();

        if (isset($data->categories) && empty($data->categories)) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível vincular as categorias ao projeto: informar as categorias que deseja vincular ao projeto",
                'data' => []
            ], 400);
        }

        if (isset($data->uuid_project) && empty($data->uuid_project)) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível vincular as categorias ao projeto: necessário informar o uuid do projeto",
                'data' => []
            ], 400);
        }

        if (is_null($data)) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível vincular as categorias ao projeto: necessário informar as categorias que vão ser vinculadas e qual o projeto",
                'data' => []
            ], 400);
        }

        if (!is_array($data->categories)) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível vincular as categorias ao projeto: o parâmetro categories precisa ser do tipo array",
                'data' => []
            ], 400);
        }

        # VERIFICAR SE EXISTE O PROJETO INFORMADO
        $project = $this->projectModel->where(['uuid_project' => $data->uuid_project, 'fk_id_user' => $this->user->id_user])->first();
        if (empty($project)) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível vincular as categorias ao projeto: projeto não encontrado",
                'data' => []
            ], 404);
        }

        # VERIFICAR SE EXISTE AS CATEGORIAS INFORMADAS
        $categoryModel = new CategoryModel();
        $selectedCategories = $categoryModel->whereIn('id_category', $data->categories)->findAll();
        if (empty($selectedCategories)) {
            return $this->respond([
                'status' => "success",
                'message' => "Não foi possível vincular as categorias ao projeto: categorias não encontradas",
                'data' => []
            ], 404);
        }

        # VINCULA AS CATEGORIAS AO PROJETO 
        $categories = [];
        foreach ($selectedCategories as $category) {
            array_push($categories, ['id_project' => $project->id_project, 'id_category' => $category->id_category]);
        }

        try {
            $projectCategoryModel = new ProjectCategoryModel();
            if ($projectCategoryModel->insertBatch($categories)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => "Categoria(s) vinculada(s)",
                    'data' => $selectedCategories
                ], 200);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => "Não foi possível vincular as categorias ao projeto",
                    'data' => [
                        'errors' => $projectCategoryModel->errors()
                    ]
                ], 400);
            }
        } catch (Exception $exception) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível vincular as categorias ao projeto: " . $exception->getMessage(),
            ], 400);
        }
    }

    public function UnlinkCategoryToProject()
    {
        $data = $this->request->getJSON();

        $uuid_project = property_exists($data, 'uuid_project') ? $data->uuid_project : null;
        $uuid_category = property_exists($data, 'uuid_category') ? $data->uuid_category : null;

        dd($data, $uuid_project, $uuid_category);
    }
}