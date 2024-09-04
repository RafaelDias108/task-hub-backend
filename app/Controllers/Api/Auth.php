<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;

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

            // $token = generateJWT($validateUser);
            $access_token = $this->GenerateAccessToken($validateUser->uuid_user);
            [$refresh_token, $expiration_date] = $this->GenerateRefreshToken($validateUser->uuid_user);

            return $this->respond([
                'status' => 'success',
                'message' => "Autenticação realizada com sucesso",
                'data' => [
                    'user' => $validateUser,
                    'refresh_token' => $refresh_token,
                    'access_token' => $access_token
                ]
            ], 200);
        } catch (\Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível fazer autenticação: " . $error->getMessage(),
                'data' => []
            ], 500);
        }
    }

    private function GenerateAccessToken(string $uuid_user)
    {
        $key = Services::getSecretKey();

        $payload = [
            'iss' => base_url(),
            'aud' => base_url(), // Audiência do token, ou seja, quem é o público-alvo do token
            'sub' => $uuid_user,
            'iat' => strtotime('now'), // Data e hora de emissão do token
            'exp' => strtotime('+15 minutes'), // Data de expiração do token
            'data' => [
                'uuid_user' => $uuid_user
            ]
        ];

        $access_token = JWT::encode($payload, $key, 'HS256');
        return $access_token;
    }

    private function GenerateRefreshToken(string $uuid_user)
    {
        $key = Services::getSecretKey();
        $expiration_date = strtotime('+1 day');

        $payload = [
            'iss' => base_url(),
            'aud' => base_url(), // Audiência do token, ou seja, quem é o público-alvo do token
            'sub' => $uuid_user,
            'iat' => strtotime('now'), // Data e hora de emissão do token
            'exp' => $expiration_date, // Data de expiração do token
            'data' => [
                'uuid_user' => $uuid_user
            ]
        ];

        $refresh_token = JWT::encode($payload, $key, 'HS256');
        return [
            'refresh_token' => $refresh_token,
            'expiration_date' => date("Y-m-d H:i:s", $expiration_date)
        ];
    }
}