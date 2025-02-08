<?php

use Config\Services;
use Firebase\JWT\JWT;

function generateJWT($user)
{
    $key = Services::getSecretKey();
    $payload = [
        'iss' => base_url(),
        'aud' => base_url(), // Audiência do token, ou seja, quem é o público-alvo do token
        'sub' => $user->id_user,
        'iat' => strtotime('now'), // Data e hora de emissão do token
        'exp' => strtotime('+5 minutes'), // Data de expiração do token
        'data' => [
            'user' => $user
        ]
    ];

    $token = JWT::encode($payload, $key, 'HS256');
    return $token;
}