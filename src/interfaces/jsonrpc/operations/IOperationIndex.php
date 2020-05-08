<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\IItem;

/**
 * Interface IOperationIndex
 *
 * @package extas\interfaces\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
interface IOperationIndex extends IItem, IOperationDispatcher
{
    const FIELD__LIMIT = 'limit';

    /**
     * @return int
     */
    public function getLimit(): int;
}
