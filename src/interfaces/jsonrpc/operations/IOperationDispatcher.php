<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\jsonrpc\IHasJsonRpcRequest;
use extas\interfaces\jsonrpc\IHasJsonRpcResponse;
use extas\interfaces\operations\IOperationDispatcher as BaseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface IOperationDispatcher
 *
 * @package extas\interfaces\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
interface IOperationDispatcher extends BaseInterface, IHasJsonRpcRequest, IHasJsonRpcResponse
{
    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface;
}
