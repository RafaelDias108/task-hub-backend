<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    use ResponseTrait;

    protected $userModel;
    protected $format = 'json';

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
                return $this->failNotFound("Usuário não encontrado");
            }

            if (!password_verify(strval($password_user), $validateUser->password_user)) {
                return $this->failUnauthorized("E-mail ou senha inválidos");
            }

            $token = generateJWT($validateUser);
            unset($validateUser->password_user);
            unset($validateUser->created_at);
            unset($validateUser->updated_at);
            unset($validateUser->deleted_at);

            return $this->respond([
                'success' => true,
                'message' => "Usuário logado com sucesso",
                'data' => [
                    'user' => $validateUser,
                    'token' => $token
                ]
            ], 201);
        } catch (\Exception $error) {
            return $this->failServerError("Ocorreu um erro na requisição: " . $error->getMessage());
        }
    }
}