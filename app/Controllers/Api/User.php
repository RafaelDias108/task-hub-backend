<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
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
        $this->userModel = new UserModel();
    }

    public function GetUserByAccessToken()
    {
        $secret_key = Services::getSecretKey();

        $header_authorization = $_SERVER['HTTP_AUTHORIZATION'];
        $header_authorization = explode(' ', $header_authorization);
        $token = $header_authorization[1];

        $tokenDecode = JWT::decode($token, new Key($secret_key, 'HS256'));
        return $tokenDecode->data->user;
    }
}