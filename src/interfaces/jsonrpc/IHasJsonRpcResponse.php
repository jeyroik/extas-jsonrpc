<?php
namespace extas\interfaces\jsonrpc;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface IHasJsonRpcResponse
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasJsonRpcResponse extends \extas\interfaces\http\IHasPsrResponse
{
    /**
     * @param string $id
     * @param $data
     * @return ResponseInterface
     */
    public function successResponse(string $id, $data): ResponseInterface;

    /**
     * @param string $id
     * @param string $message
     * @param int $code
     * @param array $data
     * @return ResponseInterface
     */
    public function errorResponse(string $id, string $message, int $code, $data = []): ResponseInterface;
}
