<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\servers\requests\IServerRequest;

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
     * @param IServerRequest $jsonRpcRequest
     * @param IResponse $jsonRpcResponse
     * @param array $data
     *
     * @return void
     */
    public function __invoke(IServerRequest $jsonRpcRequest, IResponse &$jsonRpcResponse, array $data);

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
