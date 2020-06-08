<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\operations\IOperationDispatcher as BaseInterface;
use extas\interfaces\jsonrpc\IHasPsrRequest;
use extas\interfaces\jsonrpc\IHasPsrResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface IOperationDispatcher
 *
 * @package extas\interfaces\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
interface IOperationDispatcher extends BaseInterface, IHasPsrRequest, IHasPsrResponse
{
    const SUBJECT = 'extas.jsonrpc.operation.dispatcher';
    const FIELD__OPERATION = 'operation';

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface;
}
