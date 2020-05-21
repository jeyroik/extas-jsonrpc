<?php
namespace extas\interfaces\jsonrpc\crawlers;

use extas\interfaces\IDispatcherWrapper;
use extas\interfaces\IItem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface ICrawler
 *
 * @package extas\interfaces\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
interface ICrawler extends IItem, IDispatcherWrapper
{
    public const SUBJECT = 'extas.jsonrpc.crawler';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    public function dispatch(InputInterface $input, OutputInterface $output): array;
}
