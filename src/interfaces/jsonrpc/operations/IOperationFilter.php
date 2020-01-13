<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\IItem;

/**
 * Interface IOperationFilter
 *
 * @package extas\interfaces\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
interface IOperationFilter extends IItem
{
    const SUBJECT = 'extas.jsonrpc.operation.filter';

    /**
     * @param mixed $currentValue
     * @param mixed $valueToCompareWith
     * @param string $compare
     *
     * @return bool
     */
    public function isValid($currentValue, $valueToCompareWith, string $compare): bool;
}
