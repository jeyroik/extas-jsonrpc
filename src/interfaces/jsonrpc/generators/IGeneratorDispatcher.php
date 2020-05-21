<?php
namespace extas\interfaces\jsonrpc\generators;

use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IHasInput;
use extas\interfaces\jsonrpc\IHasOutput;

/**
 * Interface IGeneratorDispatcher
 *
 * @package extas\interfaces\jsonrpc\generators
 * @author jeyroik@gmail.com
 */
interface IGeneratorDispatcher extends IItem, IHasInput, IHasOutput
{
    public const FIELD__OPERATIONS = 'jsonrpc_operations';

    /**
     * @param array $applicableClasses
     */
    public function __invoke(array $applicableClasses): void;
}
