<?php
namespace extas\interfaces\jsonrpc\generators;

use extas\interfaces\IDispatcherWrapper;
use extas\interfaces\IItem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IGenerator
 *
 * @package extas\interfaces\jsonrpc\generators
 * @author jeyroik@gmail.com
 */
interface IGenerator extends IItem, IDispatcherWrapper
{
    public const SUBJECT = 'extas.jsonrpc.generator';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $applicableClass
     */
    public function dispatch(InputInterface $input, OutputInterface $output, array $applicableClass): void;
}
