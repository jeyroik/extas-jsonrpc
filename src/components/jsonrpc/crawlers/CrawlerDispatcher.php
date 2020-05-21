<?php
namespace extas\components\jsonrpc\crawlers;

use extas\components\Item;
use extas\components\jsonrpc\THasInput;
use extas\components\jsonrpc\THasOutput;
use extas\interfaces\jsonrpc\crawlers\ICrawlerDispatcher;

/**
 * Class CrawlerDispatcher
 *
 * @package extas\components\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
abstract class CrawlerDispatcher extends Item implements ICrawlerDispatcher
{
    use THasInput;
    use THasOutput;

    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.crawler.dispatcher';
    }
}
