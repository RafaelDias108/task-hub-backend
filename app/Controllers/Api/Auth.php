<?php

namespace App\Controllers\Api;

use App\Models\TokensModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class Auth extends ResourceController
{
    use ResponseTrait;

    protected $userModel;
    protected $tokensModel;

    public function __construct()
    {
        helper('jwt');
        $this->userModel = new UserModel();
        $this->tokensModel = new TokensModel();
    }

    public function login()
    {
        try {
            $email_user = $this->request->getPost('email');
            $password_user = $this->request->getPost('password');

            $validateUser = $this->userModel->where('email_user', $email_user)->first();
            if (is_null($validateUser)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => "E-mail ou senha inválidos",
                    'data' => []
                ], 401);
            }

            if (!password_verify(strval($password_user), $validateUser->password_user)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => "E-mail ou senha inválidos",
                    'data' => []
                ], 401);
            }

            unset($validateUser->password_user);
            unset($validateUser->created_at);
            unset($validateUser->updated_at);
            unset($validateUser->deleted_at);

            // GERAR ACCESS_TOKEN
            $access_token = $this->GenerateAccessToken($validateUser->uuid_user);

            // VERIFICA SE JÁ EXISTE UM REFRESH_TOKEN GERADO
            $user_refresh_token = $this->tokensModel->orderBy('id', 'DESC')->where('id_user', $validateUser->id_user)->first();
            if (is_null($user_refresh_token) || empty($user_refresh_token)) {

                $refresh_token = $this->GenerateRefreshToken($validateUser->uuid_user);

                $token = new stdClass;
                $token->id_user = $validateUser->id_user;
                $token->refresh_token = $refresh_token->token;
                $token->expiration_date = $refresh_token->expiration_date;
                $this->tokensModel->insert($token);
            } else if ($user_refresh_token->expiration_date <= date("Y-m-d H:i:s", strtotime('now'))) {

                $this->tokensModel->delete($user_refresh_token->id);
                $refresh_token = $this->GenerateRefreshToken($validateUser->uuid_user);

                $token = new stdClass;
                $token->id_user = $validateUser->id_user;
                $token->refresh_token = $refresh_token->token;
                $token->expiration_date = $refresh_token->expiration_date;
                $this->tokensModel->insert($token);
            } else {
                $refresh_token = $user_refresh_token->refresh_token;
            }

            return $this->respond([
                'status' => 'success',
                'message' => "Autenticação realizada com sucesso",
                'data' => [
                    'user' => $validateUser,
                    'refresh_token' => $refresh_token->token,
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

    public function RefreshToken()
    {
        $refresh_token = $this->request->getPost('refresh_token');
        if (is_null($refresh_token)) {
            return $this->respond([
                'status' => 'error',
                'message' => "Refresh token inválido",
                'data' => []
            ], 401);
        }

        try {
            $user_token = $this->tokensModel->where('refresh_token', $refresh_token)->first();
            if (is_null($user_token)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => "Token de autenticação inválido",
                    'data' => []
                ], 401);
            } else if ($user_token->expiration_date > date("Y-m-d H:i:s", strtotime('now'))) {

                $user = $this->userModel->find($user_token->id_user);
                // GERAR ACCESS_TOKEN
                $access_token = $this->GenerateAccessToken($user->uuid_user);

                return $this->respond([
                    'status' => 'success',
                    'message' => "Atualização do token realizada com sucesso",
                    'data' => [
                        'access_token' => $access_token
                    ]
                ], 200);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => "Token de autenticação inválido",
                    'data' => []
                ], 401);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível realizar atualização do token: " . $error->getMessage(),
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

        $refresh_token = new stdClass;
        $refresh_token->token = JWT::encode($payload, $key, 'HS256');
        $refresh_token->expiration_date = date("Y-m-d H:i:s", $expiration_date);

        return $refresh_token;
    }
}