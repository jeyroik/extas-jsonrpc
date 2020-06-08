<?php
namespace extas\components\jsonrpc\generators;

use extas\components\Item;
use extas\components\TDispatcherWrapper;
use extas\components\THasTags;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\jsonrpc\generators\IGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Generator
 *
 * @package extas\components\jsonrpc\generators
 * @author jeyroik@gmail.com
 */
class Generator extends Item implements IGenerator
{
    use TDispatcherWrapper;
    use THasTags;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $applicableClass
     */
    public function dispatch(InputInterface $input, OutputInterface $output, array $applicableClass): void
    {
        $dispatcher = $this->buildClassWithParameters([
            IHasInput::FIELD__INPUT => $input,
            IHasOutput::FIELD__OUTPUT => $output
        ]);

        $dispatcher($applicableClass);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
