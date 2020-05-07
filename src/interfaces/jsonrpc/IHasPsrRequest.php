<?php
namespace extas\interfaces\jsonrpc;

use Psr\Http\Message\RequestInterface;

/**
 * Interface IHasPsrRequest
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IHasPsrRequest
{
    public const FIELD__PSR_REQUEST = 'psr_request';
    public const FIELD__ARGUMENTS = 'args';

    /**
     * @return IRequest
     */
    public function convertPsrToJsonRpcRequest(): IRequest;

    /**
     * Run protocols to grab all request arguments.
     */
    public function applyProtocols(): void;

    /**
     * @return RequestInterface
     */
    public function getPsrRequest(): RequestInterface;

    /**
     * @return array
     */
    public function getArguments(): array;
}
