<?php
namespace extas\components\jsonrpc;

use extas\components\protocols\ProtocolRunner;
use extas\interfaces\jsonrpc\IHasPsrRequest;
use extas\interfaces\jsonrpc\IRequest;
use Psr\Http\Message\RequestInterface;

/**
 * Trait THasPsrRequest
 *
 * @property array $config
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
trait THasPsrRequest
{
    /**
     * @return IRequest
     */
    public function convertPsrToJsonRpcRequest(): IRequest
    {
        return Request::fromHttp($this->getPsrRequest());
    }

    /**
     * Run protocols to grab all request arguments.
     */
    public function applyProtocols(): void
    {
        ProtocolRunner::run(
            $this->config[IHasPsrRequest::FIELD__ARGUMENTS],
            $this->config[IHasPsrRequest::FIELD__PSR_REQUEST]
        );
    }

    /**
     * @return RequestInterface
     */
    public function getPsrRequest(): RequestInterface
    {
        return $this->config[IHasPsrRequest::FIELD__PSR_REQUEST];
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->config[IHasPsrRequest::FIELD__ARGUMENTS] ?? [];
    }
}
