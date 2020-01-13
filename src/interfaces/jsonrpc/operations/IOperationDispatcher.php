<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\IItem;

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
