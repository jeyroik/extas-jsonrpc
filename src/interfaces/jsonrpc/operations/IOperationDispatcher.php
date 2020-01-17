<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;

/**
 * Interface IOperationDispatcher
 *
 * @package extas\interfaces\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
interface IOperationDispatcher extends IItem
{
    const SUBJECT = 'extas.jsonrpc.operation.dispatcher';
    const FIELD__OPERATION = 'operation';

    /**
     * @param IServerRequest $serverRequest
     * @param IServerResponse $serverResponse
     *
     * @return void
     */
    public function __invoke(IServerRequest $serverRequest, IServerResponse &$serverResponse);

    /**
     * @return IOperation|null
     */
    public function getOperation(): ?IOperation;

    /**
     * @param IOperation $operation
     *
     * @return IOperationDispatcher
     */
    public function setOperation(IOperation $operation): IOperationDispatcher;
}
