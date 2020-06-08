<?php
namespace extas\interfaces\jsonrpc\generators;

use extas\interfaces\IHasIO;
use extas\interfaces\IItem;

/**
 * Interface IGeneratorDispatcher
 *
 * @package extas\interfaces\jsonrpc\generators
 * @author jeyroik@gmail.com
 */
interface IGeneratorDispatcher extends IItem, IHasIO
{
    public const FIELD__OPERATIONS = 'jsonrpc_operations';

    /**
     * @param array $applicableClasses
     */
    public function __invoke(array $applicableClasses): void;
}
