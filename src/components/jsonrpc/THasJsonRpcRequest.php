<?php
namespace extas\components\jsonrpc;

use extas\interfaces\jsonrpc\IRequest;

/**
 * Trait THasJsonRpcRequest
 *
 * @package extas\components\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasJsonRpcRequest
{
    use \extas\components\http\THasPsrRequest;

    /**
     * @return IRequest
     */
    public function getJsonRpcRequest(): IRequest
    {
        return Request::fromHttp($this->getPsrRequest());
    }
}
