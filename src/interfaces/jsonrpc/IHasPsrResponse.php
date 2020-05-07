<?php
namespace extas\interfaces\jsonrpc;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface IHasResponse
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IHasPsrResponse
{
    public const FIELD__PSR_RESPONSE = 'psr_response';

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

    /**
     * @return ResponseInterface
     */
    public function getPsrResponse(): ResponseInterface;

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setPsrResponse(ResponseInterface $response);
}
