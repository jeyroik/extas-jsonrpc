<?php
namespace extas\interfaces\jsonrpc;

/**
 * Interface IHasJsonRpcRequest
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasJsonRpcRequest extends \extas\interfaces\http\IHasPsrRequest
{
    /**
     * @return IRequest
     */
    public function getJsonRpcRequest(): IRequest;
}
