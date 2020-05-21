<?php
namespace extas\interfaces\jsonrpc\crawlers;

use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IHasInput;
use extas\interfaces\jsonrpc\IHasOutput;

/**
 * Interface ICrawlerDispatcher
 *
 * @package extas\interfaces\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
interface ICrawlerDispatcher extends IItem, IHasInput, IHasOutput
{
    /**
     * @return array
     */
    public function __invoke(): array;
}
