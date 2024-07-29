<?php

namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class AuthFilter implements FilterInterface
{
    use ResponseTrait;

    public $response;

    public function __construct()
    {
        $this->response = Services::response();
        $this->response->setContentType('application/json');
    }
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $secret_key = Services::getSecretKey();
            $header_authorization = $request->getServer('HTTP_AUTHORIZATION');

            if (is_null($header_authorization)) {

                $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
                $this->response->setBody(json_encode([
                    'success' => false,
                    'message' => "Token inválido. Acesso não autorizado."
                ]));
                return $this->response;
            }

            $header_authorization = explode(' ', $header_authorization);
            $token = $header_authorization[1];

            JWT::decode($token, new Key($secret_key, 'HS256'));
        } catch (ExpiredException $expired) {

            $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            $this->response->setBody(json_encode([
                'success' => false,
                'message' => 'Token expirado'
            ]));
            return $this->response;
        } catch (SignatureInvalidException $signatureInvalid) {

            $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            $this->response->setBody(json_encode([
                'success' => false,
                'message' => 'Token inválido'
            ]));
            return $this->response;
        } catch (\Exception $error) {

            $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            $this->response->setBody(json_encode([
                'success' => false,
                'message' => 'Erro: ' . $error->getMessage()
            ]));
            return $this->response;
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}