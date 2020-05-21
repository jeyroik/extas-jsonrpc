<?php
namespace extas\interfaces\jsonrpc;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IHasOutput
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IHasOutput
{
    public const FIELD__OUTPUT = 'output';

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface;

    /**
     * @param OutputInterface $output
     * @return $this
     */
    public function setOutput(OutputInterface $output);

    /**
     * @param array $lines
     */
    public function writeLn(array $lines): void;
}
