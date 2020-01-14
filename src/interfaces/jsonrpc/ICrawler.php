<?php
namespace extas\interfaces\jsonrpc;

use extas\interfaces\plugins\IPlugin;

/**
 * Interface ICrawler
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface ICrawler
{
    const SUBJECT = 'extas.jsonrpc.crawler';

    /**
     * @param string $path
     * @param string $prefix
     *
     * @return IPlugin[]
     */
    public function crawlPlugins(string $path, string $prefix): array;
}
