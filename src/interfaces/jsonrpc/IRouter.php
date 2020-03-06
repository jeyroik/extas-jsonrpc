<?php
namespace extas\interfaces\jsonrpc;

use extas\interfaces\IItem;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface IRouter
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IRouter extends IItem
{
    const SUBJECT = 'extas.jsonrpc.router';

    const ROUTE__DEFAULT = 'app.index';
    const ROUTE__ALL = 'operation.all';

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOperation(string $name): bool;

    /**
     * @param RequestInterface $httpRequest
     * @param ResponseInterface $httpResponse
     * @param IRequest $jsonRpcRequest
     *
     * @return ResponseInterface
     */
    public function dispatch(
        RequestInterface $httpRequest,
        ResponseInterface $httpResponse,
        IRequest $jsonRpcRequest = null
    ): ResponseInterface;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function getSpecs(RequestInterface $request, ResponseInterface $response): ResponseInterface;
}
