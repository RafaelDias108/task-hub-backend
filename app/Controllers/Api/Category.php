<?php

namespace App\Controllers\Api;

use App\Models\CategoryModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class Category extends ResourceController
{
    use ResponseTrait;

    protected $categoryModel;
    private $userController;
    private $loggedInUser;

    public function __construct()
    {
        $this->categoryModel  = new CategoryModel();
        $this->userController = new User();
        $this->loggedInUser = $this->userController->GetUserByAccessToken();
    }

    public function index($uuid_category = null)
    {
        try {
            if (is_null($uuid_category)) {

                $categories = $this->categoryModel->where(['id_user' => $this->loggedInUser->id_user])->findAll();
                if (empty($categories)) {
                    return $this->respond([
                        'status' => 'success',
                        'message' => "Categorias não encontradas",
                        'data' => []
                    ], 404);
                }

                return $this->respond([
                    'status' => 'success',
                    'data' => $categories
                ], 200);
            } else {
                $category = $this->categoryModel->where(['uuid_category' => $uuid_category, 'id_user' => $this->loggedInUser->id_user])->first();

                if (empty($category)) {
                    return $this->respond([
                        'status' => 'success',
                        'message' => "Categoria não encontrada",
                        'data' => []
                    ], 404);
                }

                return $this->respond([
                    'status' => 'success',
                    'data' => $category
                ], 200);
            }
        } catch (Exception $exception) {
            return $this->respond([
                'status' => 'error',
                'message' => "Ocorreu um erro ao buscar categorias: " . $exception->getMessage(),
                'data' => []
            ], 400);
        }
    }

    public function NewCategory()
    {
        $categoryData = $this->request->getJSON();
        $categoryData->id_user = intval($this->loggedInUser->id_user);
        $categoryData->uuid_category = GenerateUUID();

        try {
            if ($this->categoryModel->insert($categoryData)) {
                $category = $this->categoryModel->find($this->categoryModel->getInsertID());
                return $this->respond([
                    'status' => 'success',
                    'message' => "Categoria criada com sucesso",
                    'data' => $category
                ], 201);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => "Não foi possível criar categoria",
                    'data' =>  $this->categoryModel->errors()
                ], 400);
            }
        } catch (Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível criar categoria: " . $error->getMessage(),
            ], 400);
        }
    }

    public function UpdateCategory($uuid_category = null)
    {
        if (is_null($uuid_category)) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível atualizar a categoria: UUID inválido",
            ], 400);
        }

        try {
            $category = $this->categoryModel->where(['uuid_category' => $uuid_category, 'id_user' => $this->loggedInUser->id_user])->first();
            if (is_null($category)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Não foi possível atualizar a categoria: categoria não encontrada",
                    'data' => []
                ], 404);
            }

            $data = $this->request->getJSON();
            $isUpdated = $this->categoryModel->update($category->id_category, $data);
            if ($isUpdated) {
                $categoryUpdated = $this->categoryModel->find($category->id_category);
                return $this->respond([
                    'status' => "success",
                    'message' => "Categoria atualizada",
                    'data' => $categoryUpdated
                ], 200);
            }
        } catch (Exception $error) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível atualizar a categoria: " . $error->getMessage()
            ], 400);
        }
    }

    public function DeleteCategory($uuid_category = null)
    {
        if (is_null($uuid_category)) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível excluir a categoria: UUID inválido",
            ], 400);
        }

        try {
            $category = $this->categoryModel->where(['uuid_category' => $uuid_category, 'id_user' => $this->loggedInUser->id_user])->first();
            if (is_null($category)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Não foi possível excluir a categoria: categoria não encontrada",
                    'data' => []
                ], 404);
            }

            if ($this->categoryModel->delete($category->id_category)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Categoria excluída",
                    'data' => $category
                ], 200);
            }
        } catch (Exception $error) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível excluir a categoria: " . $error->getMessage()
            ], 400);
        }
    }
}