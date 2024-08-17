<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        helper('jwt');
        $this->userModel = new UserModel();
    }

    public function login()
    {
        try {
            $email_user = $this->request->getPost('email');
            $password_user = $this->request->getPost('password');

            $validateUser = $this->userModel->where('email_user', $email_user)->first();
            if (is_null($validateUser)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => "E-mail ou senha inválidos",
                    'data' => []
                ], 401);
            }

            if (!password_verify(strval($password_user), $validateUser->password_user)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => "E-mail ou senha inválidos",
                    'data' => []
                ], 401);
            }

            unset($validateUser->password_user);
            unset($validateUser->created_at);
            unset($validateUser->updated_at);
            unset($validateUser->deleted_at);

            $token = generateJWT($validateUser);

            return $this->respond([
                'status' => 'success',
                'message' => "Autenticação realizada com sucesso",
                'data' => [
                    'user' => $validateUser,
                    'token' => $token
                ]
            ], 200);
        } catch (\Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Ocorreu um erro na requisição: " . $error->getMessage(),
                'data' => []
            ], 500);
        }
    }
}