<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User extends ResourceController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        helper("uuid_helper");
        $this->userModel = new UserModel();
    }

    public function index($uuid = null)
    {
        $authenticatedUser = $this->GetUserByAccessToken();
        if (!is_null($uuid) && $uuid == $authenticatedUser->uuid_user) {

            try {
                $user = $this->userModel->select('uuid_user, firstname_user, lastname_user, email_user')->where('uuid_user', $uuid)->find();
                if (empty($user)) {
                    return $this->respond([
                        'status'   => 'error',
                        'message' => 'Usuário não encontrado',
                        'data' => []
                    ], 404);
                }

                return $this->respond([
                    'status'   => 'success',
                    'data' => $user
                ], 200);
            } catch (\Exception $exception) {
                return $this->respond([
                    'status'   => 'error',
                    'message' => "Erro ao buscar os dados do usuário: " . $exception->getMessage(),
                    'data' => []
                ], 400);
            }
        } else {
            return $this->respond([
                'status'   => 'error',
                'message' => "Erro ao buscar os dados do usuário: UUID do usuário inválido",
                'data' => []
            ], 400);
        }
    }

    public function NewUser()
    {
        $userData = $this->request->getJSON();
        $userData->uuid_user = GenerateUUID();

        if (isset($userData->password_user) && !empty($userData->password_user)) {

            $passwordVerified = $this->_VerifyPasswordRules($userData->password_user);
            if (!empty($passwordVerified)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => "Não foi possível criar uma conta: a senha não corresponde as regras de segurança",
                    'data' => $passwordVerified
                ], 400);
            }

            $userData->password_user = password_hash($userData->password_user, PASSWORD_BCRYPT);
        }

        try {
            if ($this->userModel->insert($userData)) {
                $user = $this->userModel->select('uuid_user, firstname_user, lastname_user, email_user')->find($this->userModel->getInsertID());
                return $this->respond([
                    'status' => 'success',
                    'message' => "Conta criada com sucesso",
                    'data' => $user
                ], 201);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => "Não foi possível criar a conta",
                    'data' => $this->userModel->errors()
                ]);
            }
        } catch (DatabaseException $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível criar uma conta: " . $error->getMessage(),
            ], 400);
        } catch (\Throwable $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível criar uma conta: " . $error->getMessage(),
            ], 400);
        }
    }

    public function UpdateUser($uuid = null)
    {
        $authenticatedUser = $this->GetUserByAccessToken();
        if (is_null($uuid) || $uuid != $authenticatedUser->uuid_user) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível atualizar a conta: UUID inválido",
            ], 400);
        }

        try {
            $user = $this->userModel->where('uuid_user', $uuid)->first();
            if (is_null($user)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Não foi possível atualizar a conta: Conta não encontrada",
                    'data' => []
                ], 404);
            }

            $data = $this->request->getJSON();
            if (isset($data->password_user)) {
                unset($data->password_user);
            }

            $isUpdated = $this->userModel->update($user->id_user, $data);
            if ($isUpdated) {
                $userUpdated = $this->userModel->select("uuid_user, firstname_user, lastname_user, email_user")->find($user->id_user);
                return $this->respond([
                    'status' => "success",
                    'message' => "Conta atualizada",
                    'data' => $userUpdated
                ], 200);
            }
        } catch (\Exception $exception) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível atualizar a conta: " . $exception->getMessage()
            ], 400);
        }
    }

    public function DeleteUser() {}

    public function GetUserByAccessToken()
    {
        $secret_key = Services::getSecretKey();

        $header_authorization = $_SERVER['HTTP_AUTHORIZATION'];
        $header_authorization = explode(' ', $header_authorization);
        $token = $header_authorization[1];

        $tokenDecode = JWT::decode($token, new Key($secret_key, 'HS256'));
        return $tokenDecode->data->user;
    }

    private function _VerifyPasswordRules($password)
    {
        $erros = [];

        if (preg_match('/\d/', $password, $matche) == 0) {
            array_push($erros, "deve conter pelo menos um número");
        }

        if (preg_match('/[A-Z]/', $password, $matche) == 0) {
            array_push($erros, "deve conter pelo menos uma letra maiúscula");
        }

        if (preg_match('/[a-z]/', $password, $matche) == 0) {
            array_push($erros, "deve conter pelo menos uma letra minúscula");
        }

        if (preg_match('/[\W_]/', $password, $matche) == 0) {
            array_push($erros, "deve conter pelo menos um caractere especial");
        }

        if (preg_match('/.{8,}/', $password, $matche) == 0) {
            array_push($erros, "deve conter no mínimo 8 caracteres");
        }

        return $erros;
    }
}