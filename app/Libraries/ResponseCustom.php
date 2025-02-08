<?php

namespace App\Libraries;

use CodeIgniter\API\ResponseTrait;

class ResponseCustom
{
    use ResponseTrait;

    public function ResponseSuccess(string $message = "", array $data = [], int $httpCode = 200)
    {
        $respond = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];

        if (empty($message)) {
            unset($respond['message']);
        }

        return $this->respond($respond, $httpCode);
    }

    public function ResponseError(string $message, array $data = [], int $httpCode = 400)
    {
        return $this->respond([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $httpCode);
    }
}