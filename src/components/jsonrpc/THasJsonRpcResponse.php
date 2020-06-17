<?php
namespace extas\components\jsonrpc;

use extas\interfaces\jsonrpc\IResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait THasJsonRpcResponse
 *
 * @package extas\components\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasJsonRpcResponse
{
    use \extas\components\http\THasPsrResponse;

    /**
     * @param string $id
     * @param $data
     * @return ResponseInterface
     */
    public function successResponse(string $id, $data): ResponseInterface
    {
        $response = $this->getPsrResponse();
        $response->getBody()->write(json_encode(
            [
                IResponse::RESPONSE__ID => $id,
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__RESULT => $data
            ]
        ));

        return $response;
    }

    /**
     * @param string $id
     * @param string $message
     * @param int $code
     * @param array $data
     * @return ResponseInterface
     */
    public function errorResponse(string $id, string $message, int $code, $data = []): ResponseInterface
    {
        $response = $this->getPsrResponse();
        $response->getBody()->write(json_encode(
            [
                IResponse::RESPONSE__ID => $id,
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__ERROR => [
                    IResponse::RESPONSE__ERROR_CODE => $code,
                    IResponse::RESPONSE__ERROR_DATA => $data,
                    IResponse::RESPONSE__ERROR_MESSAGE => $message
                ]
            ]
        ));

        return $response;
    }
}
