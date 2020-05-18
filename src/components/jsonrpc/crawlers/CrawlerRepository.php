<?php
namespace extas\components\jsonrpc\crawlers;

use extas\components\repositories\Repository;
use extas\interfaces\jsonrpc\crawlers\ICrawlerRepository;

/**
 * Class CrawlerRepository
 *
 * @package extas\components\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
class CrawlerRepository extends Repository implements ICrawlerRepository
{
    protected string $name = 'jsonrpc_crawlers';
    protected string $scope = 'extas';
    protected string $pk = Crawler::FIELD__NAME;
    protected string $itemClass = Crawler::class;
}
