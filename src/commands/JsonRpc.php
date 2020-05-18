<?php
namespace extas\commands;

use extas\components\Item;

/**
 * Class JsonRpc
 *
 * @method jsonRpcCrawlerRepository()
 * @method jsonRpcGeneratorRepository()
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class JsonRpc extends Item
{
    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc';
    }
}
