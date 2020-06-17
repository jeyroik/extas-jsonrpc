<?php
namespace extas\interfaces\jsonrpc;

use extas\interfaces\IItem;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface IRouter
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IRouter extends IItem, IHasJsonRpcRequest, IHasJsonRpcResponse
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
     * @return ResponseInterface
     */
    public function dispatch(): ResponseInterface;

    /**
     * @return ResponseInterface
     */
    public function getSpecs(): ResponseInterface;
}
