<?php
namespace extas\components\jsonrpc\operations;

use extas\components\jsonrpc\THasPsrRequest;
use extas\components\jsonrpc\THasPsrResponse;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;
use extas\interfaces\operations\IJsonRpcOperation;
use extas\interfaces\repositories\IRepository;
use extas\components\operations\OperationDispatcher as BaseDispatcher;

/**
 * Class OperationDispatcher
 *
 * @package extas\components\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
abstract class OperationDispatcher extends BaseDispatcher implements IOperationDispatcher
{
    use THasPsrRequest;
    use THasPsrResponse;

    /**
     * @return IRepository
     */
    protected function getItemRepo(): IRepository
    {
        /**
         * @var IJsonRpcOperation $operation
         */
        $operation = $this->getOperation();
        return $operation->getItemRepository();
    }
}
