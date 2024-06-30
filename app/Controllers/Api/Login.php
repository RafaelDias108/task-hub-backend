<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Login extends ResourceController
{
    use ResponseTrait;

    protected $userModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function auth()
    {
        $uuid_user = false;
        $requestBody = json_decode($this->request->getBody(), true);

        if ($requestBody) {
            if (!isset($requestBody['uuid'])) {
                return $this->fail('Corpo da requisição fora do padrão: Necessário ser enviado o uuid para retornar um usuário específico');
            }
            $uuid_user = $requestBody['uuid'];
        }

        try {
            if ($uuid_user) {
                $loginData = $this->userModel->where('uuid_user', $uuid_user)->find();
                if (empty($loginData)) {
                    return $this->failNotFound('Usuário não encontrado');
                }
                unset($loginData->password_login);
                return $this->respond($loginData);
            };

            $users = $this->userModel->findAll();
            if (!$users) {
                return $this->failNotFound('Não há usuários cadastrados');
            }
            return $this->respond($users);
        } catch (\Exception $error) {
            return $this->failServerError('Ocorreu um erro no requisição: ' . $error->getMessage());
        }
    }
}