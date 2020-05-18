<?php
namespace extas\components\plugins;

use extas\components\jsonrpc\crawlers\Crawler;
use extas\interfaces\jsonrpc\crawlers\ICrawlerRepository;

/**
 * Class PluginInstallJsonRpcCrawlers
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallJsonRpcCrawlers extends PluginInstallDefault
{
    protected string $selfSection = 'jsonrpc_crawlers';
    protected string $selfName = 'jsonrpc crawler';
    protected string $selfRepositoryClass = ICrawlerRepository::class;
    protected string $selfUID = Crawler::FIELD__NAME;
    protected string $selfItemClass = Crawler::class;
}
