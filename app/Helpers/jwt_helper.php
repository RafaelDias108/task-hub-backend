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
        'iat' => time(), // Data e hora de emissão do token
        'exp' => time() + 60, // Data de expiração do token
        'data' => [
            'user' => [
                'email' => $user->email_user,
                'firstname' => $user->firstname_user,
                'lastname' => $user->lastname_user,
            ]
        ]
    ];

    $token = JWT::encode($payload, $key, 'HS256');
    return $token;
}