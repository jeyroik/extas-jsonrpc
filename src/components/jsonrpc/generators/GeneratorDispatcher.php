<?php
namespace extas\components\jsonrpc\generators;

use extas\components\Item;
use extas\components\jsonrpc\THasInput;
use extas\components\jsonrpc\THasOutput;
use extas\interfaces\jsonrpc\generators\IGeneratorDispatcher;

/**
 * Class GeneratorDispatcher
 *
 * @package extas\components\jsonrpc\generators
 * @author jeyroik@gmail.com
 */
abstract class GeneratorDispatcher extends Item implements IGeneratorDispatcher
{
    use THasInput;
    use THasOutput;

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.generator.dispatcher';
    }
}
