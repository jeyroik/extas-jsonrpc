<?php
namespace extas\components\jsonrpc\operations;

use extas\components\Item;
use extas\components\jsonrpc\THasPsrRequest;
use extas\components\jsonrpc\THasPsrResponse;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;
use extas\interfaces\repositories\IRepository;

/**
 * Class OperationDispatcher
 *
 * @package extas\components\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
abstract class OperationDispatcher extends Item implements IOperationDispatcher
{
    use THasPsrRequest;
    use THasPsrResponse;

    /**
     * @return IOperation|null
     */
    public function getOperation(): ?IOperation
    {
        return $this->config[static::FIELD__OPERATION] ?? null;
    }

    /**
     * @return IRepository
     */
    protected function getItemRepo(): IRepository
    {
        $repoMethod = $this->getOperation()->getItemRepo();
        return $this->$repoMethod();
    }
}
