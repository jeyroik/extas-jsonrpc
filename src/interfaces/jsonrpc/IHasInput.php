<?php
namespace extas\interfaces\jsonrpc;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Interface IHasInput
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IHasInput
{
    public const FIELD__INPUT = 'input';

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface;
}
